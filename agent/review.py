import os
import json
import requests
import anthropic
from github import Github

# ── Environment variables ─────────────────────────────────────────────────────
GITHUB_TOKEN = os.environ["GITHUB_TOKEN"]
ANTHROPIC_API_KEY = os.environ["ANTHROPIC_API_KEY"]
PR_NUMBER = int(os.environ["PR_NUMBER"])
REPO_NAME = os.environ["REPO_NAME"]
BASE_SHA = os.environ["BASE_SHA"]
HEAD_SHA = os.environ["HEAD_SHA"]

# ── GitHub API headers ────────────────────────────────────────────────────────
GITHUB_HEADERS = {
    "Authorization": f"Bearer {GITHUB_TOKEN}",
    "Accept": "application/vnd.github+json",
    "X-GitHub-Api-Version": "2022-11-28",
}

GITHUB_API = "https://api.github.com"


# ── Step 1: Load the rulebook ─────────────────────────────────────────────────
def load_rulebook():
    rulebook_path = "review-rules.md"
    if not os.path.exists(rulebook_path):
        post_warning_comment(
            "⚠️ No `review-rules.md` rulebook found in this repository. Automated review skipped."
        )
        exit(0)
    with open(rulebook_path, "r") as f:
        return f.read()


# ── Step 2: Get already-reviewed commits ──────────────────────────────────────
def get_reviewed_commits():
    """Read existing PR comments to find which commits were already reviewed."""
    url = f"{GITHUB_API}/repos/{REPO_NAME}/issues/{PR_NUMBER}/comments"
    response = requests.get(url, headers=GITHUB_HEADERS)
    comments = response.json()

    reviewed = set()
    for comment in comments:
        if "<!-- reviewed-commit:" in comment.get("body", ""):
            body = comment["body"]
            start = body.index("<!-- reviewed-commit:") + len("<!-- reviewed-commit:")
            end = body.index("-->", start)
            reviewed.add(body[start:end].strip())
    return reviewed


# ── Step 3: Get the diff ──────────────────────────────────────────────────────
def get_diff():
    url = f"{GITHUB_API}/repos/{REPO_NAME}/compare/{BASE_SHA}...{HEAD_SHA}"
    response = requests.get(
        url, headers={**GITHUB_HEADERS, "Accept": "application/vnd.github.v3.diff"}
    )
    return response.text


def is_trivial_diff(diff: str) -> bool:
    """Returns True if the diff contains no substantive code changes."""
    lines = [l for l in diff.splitlines() if l.startswith("+") or l.startswith("-")]
    substantive = [l for l in lines if l.strip() not in ("+", "-", "+++", "---")]
    return len(substantive) == 0


def truncate_diff(diff: str, max_chars: int = 80000) -> str:
    """Truncate diff if it exceeds token-safe size."""
    if len(diff) <= max_chars:
        return diff
    truncated = diff[:max_chars]
    return (
        truncated
        + "\n\n[DIFF TRUNCATED: This diff exceeded the maximum reviewable size. Only the first portion was reviewed.]"
    )


# ── Step 4: Call Claude API ───────────────────────────────────────────────────
def call_claude(diff: str, rulebook: str) -> dict:
    client = anthropic.Anthropic(api_key=ANTHROPIC_API_KEY)
    g = Github(GITHUB_TOKEN)

    # ── Real tool functions ───────────────────────────────────────────────────

    def fetch_file(repo_name: str, filepath: str) -> str:
        try:
            repo = g.get_repo(repo_name)
            file = repo.get_contents(filepath)
            return file.decoded_content.decode("utf-8")
        except Exception as e:
            return f"Error fetching file: {str(e)}"

    def fetch_repo_structure(repo_name: str) -> str:
        try:
            repo = g.get_repo(repo_name)
            contents = repo.get_contents("")
            structure = []
            while contents:
                item = contents.pop(0)
                if item.type == "dir":
                    structure.append(f"📁 {item.path}/")
                    contents.extend(repo.get_contents(item.path))
                else:
                    structure.append(f"   {item.path}")
            return "\n".join(structure)
        except Exception as e:
            return f"Error fetching structure: {str(e)}"

    # ── Tool definitions ──────────────────────────────────────────────────────

    tools = [
        {
            "name": "fetch_file",
            "description": "Fetch a specific file from the repository when you need more context to understand the code changes",
            "input_schema": {
                "type": "object",
                "properties": {
                    "repo_name": {
                        "type": "string",
                        "description": "Repository name e.g. org/repo",
                    },
                    "filepath": {"type": "string", "description": "Path to the file"},
                },
                "required": ["repo_name", "filepath"],
            },
        },
        {
            "name": "fetch_repo_structure",
            "description": "Fetch the repository directory structure when you need to understand how the codebase is organized",
            "input_schema": {
                "type": "object",
                "properties": {
                    "repo_name": {
                        "type": "string",
                        "description": "Repository name e.g. org/repo",
                    }
                },
                "required": ["repo_name"],
            },
        },
    ]

    # ── Tool dispatcher ───────────────────────────────────────────────────────

    def dispatch_tool(name: str, args: dict) -> str:
        if name == "fetch_file":
            return fetch_file(**args)
        elif name == "fetch_repo_structure":
            return fetch_repo_structure(**args)
        else:
            return f"Unknown tool: {name}"

    # ── Agent loop ────────────────────────────────────────────────────────────

    MAX_TOOL_CALLS = 10
    tool_call_count = 0

    system_prompt = """You are an expert code reviewer. You will be given a PR diff and a rulebook.

Your process:
1. Analyze the diff against the rulebook
2. If you need more context (e.g. to understand a pattern or check an import), fetch relevant files
3. Once you have enough information, produce your final structured review

You MUST return valid JSON in exactly this format and nothing else:

{
  "critical": [
    {
      "file": "path/to/file.php",
      "line": 42,
      "comment": "Your critical feedback here.",
      "suggestion": "Optional: corrected code snippet"
    }
  ],
  "suggested": [
    {
      "file": "path/to/file.php",
      "line": 15,
      "comment": "Your suggestion here.",
      "suggestion": "Optional: corrected code snippet"
    }
  ]
}

Flag a MAXIMUM of 5 critical issues and 5 suggestions.
Only comment on lines that appear in the diff.
Return JSON only when you are done — no markdown, no preamble."""

    messages = [
        {
            "role": "user",
            "content": f"RULEBOOK:\n{rulebook}\n\n---\n\nPULL REQUEST DIFF:\n{diff}\n\nRepository: {REPO_NAME}\n\nReview this diff against the rulebook.",
        }
    ]

    while True:
        if tool_call_count >= MAX_TOOL_CALLS:
            print(f"⚠️ Tool call limit reached. Proceeding with available context.")
            break

        response = client.messages.create(
            model="claude-sonnet-4-20250514",
            max_tokens=4000,
            system=system_prompt,
            tools=tools,
            messages=messages,
        )

        # Add assistant response to messages
        messages.append({"role": "assistant", "content": response.content})

        if response.stop_reason == "end_turn":
            # Agent is done — extract the final JSON
            for block in response.content:
                if hasattr(block, "text"):
                    raw = block.text.strip()
                    if raw.startswith("```"):
                        raw = raw.split("\n", 1)[1].rsplit("```", 1)[0]
                    return json.loads(raw)

        elif response.stop_reason == "tool_use":
            # Process tool calls
            tool_results = []
            for block in response.content:
                if block.type == "tool_use":
                    tool_call_count += 1
                    print(
                        f"🔧 Tool call #{tool_call_count}: {block.name}({block.input})"
                    )
                    result = dispatch_tool(block.name, block.input)
                    print(f"   ↳ {result[:100]}{'...' if len(result) > 100 else ''}")
                    tool_results.append(
                        {
                            "type": "tool_result",
                            "tool_use_id": block.id,
                            "content": result,
                        }
                    )

            messages.append({"role": "user", "content": tool_results})

        else:
            print(f"Unexpected stop reason: {response.stop_reason}")
            break

    # Fallback if loop exits without a clean end_turn
    raise Exception("Agent loop ended without producing a review.")


# ── Step 5: Get PR files for line mapping ─────────────────────────────────────
def get_pr_files():
    url = f"{GITHUB_API}/repos/{REPO_NAME}/pulls/{PR_NUMBER}/files"
    response = requests.get(url, headers=GITHUB_HEADERS)
    return response.json()


def build_line_position_map(pr_files):
    """
    GitHub requires a 'position' value (line number within the diff hunk)
    rather than the actual file line number when posting inline comments.
    This builds a map: {filename: {line_number: position}}
    """
    mapping = {}
    for f in pr_files:
        filename = f["filename"]
        patch = f.get("patch", "")
        if not patch:
            continue

        mapping[filename] = {}
        position = 0
        current_line = 0

        for patch_line in patch.splitlines():
            position += 1
            if patch_line.startswith("@@"):
                # Parse the hunk header to get starting line number
                # Format: @@ -old_start,old_count +new_start,new_count @@
                try:
                    new_part = patch_line.split("+")[1].split("@@")[0].strip()
                    current_line = int(new_part.split(",")[0]) - 1
                except (IndexError, ValueError):
                    current_line = 0
            elif patch_line.startswith("-"):
                pass  # Removed line — no new line number
            else:
                current_line += 1
                mapping[filename][current_line] = position

    return mapping


# ── Step 6: Post comments to GitHub ──────────────────────────────────────────
def post_summary_comment(critical_count: int, suggested_count: int, commit_sha: str):
    if critical_count == 0 and suggested_count == 0:
        body = f"""## 🤖 AI Code Review Summary

**Repository:** {REPO_NAME}
**Rulebook:** review-rules.md v1.0
**Commit reviewed:** `{commit_sha[:7]}`

✅ No critical issues or suggestions found. Looks good!

<!-- reviewed-commit:{commit_sha} -->"""
    else:
        extra_note = ""
        if critical_count == 5:
            extra_note = "\n> ⚠️ There may be additional critical issues beyond the 5 shown. Please review carefully."

        body = f"""## 🤖 AI Code Review Summary

**Repository:** {REPO_NAME}
**Rulebook:** review-rules.md v1.0
**Commit reviewed:** `{commit_sha[:7]}`

🔴 **Critical Issues:** {critical_count}
🟡 **Suggestions:** {suggested_count}

Please resolve all 🔴 Critical issues before requesting a human review.{extra_note}

<!-- reviewed-commit:{commit_sha} -->"""

    url = f"{GITHUB_API}/repos/{REPO_NAME}/issues/{PR_NUMBER}/comments"
    requests.post(url, headers=GITHUB_HEADERS, json={"body": body})


def post_inline_comments(feedback: dict, line_map: dict):
    """Post inline review comments on specific lines of the diff."""
    comments = []

    for item in feedback.get("critical", []):
        file = item.get("file", "")
        line = item.get("line", 0)
        comment_text = item.get("comment", "")
        suggestion = item.get("suggestion", "")

        position = line_map.get(file, {}).get(line)
        if not position:
            continue

        body = f"🔴 **Critical**\n\n{comment_text}"
        if suggestion:
            body += f"\n\n```suggestion\n{suggestion}\n```"

        comments.append({"path": file, "position": position, "body": body})

    for item in feedback.get("suggested", []):
        file = item.get("file", "")
        line = item.get("line", 0)
        comment_text = item.get("comment", "")
        suggestion = item.get("suggestion", "")

        position = line_map.get(file, {}).get(line)
        if not position:
            continue

        body = f"🟡 **Suggestion**\n\n{comment_text}"
        if suggestion:
            body += f"\n\n```suggestion\n{suggestion}\n```"

        comments.append({"path": file, "position": position, "body": body})

    if not comments:
        return

    url = f"{GITHUB_API}/repos/{REPO_NAME}/pulls/{PR_NUMBER}/reviews"
    payload = {"commit_id": HEAD_SHA, "event": "COMMENT", "comments": comments}
    response = requests.post(url, headers=GITHUB_HEADERS, json=payload)
    if response.status_code not in (200, 201):
        print(
            f"Warning: Failed to post inline comments: {response.status_code} {response.text}"
        )


def post_warning_comment(message: str):
    url = f"{GITHUB_API}/repos/{REPO_NAME}/issues/{PR_NUMBER}/comments"
    requests.post(url, headers=GITHUB_HEADERS, json={"body": message})


# ── Main ──────────────────────────────────────────────────────────────────────
def main():
    print("Starting AI Code Review Agent...")

    # Step 1: Load rulebook
    print("Loading rulebook...")
    rulebook = load_rulebook()

    # Step 2: Check if this commit was already reviewed
    print("Checking previously reviewed commits...")
    reviewed_commits = get_reviewed_commits()
    if HEAD_SHA in reviewed_commits:
        print(f"Commit {HEAD_SHA[:7]} already reviewed. Skipping.")
        return

    # Step 3: Get the diff
    print("Fetching diff...")
    diff = get_diff()

    if is_trivial_diff(diff):
        print("Trivial diff detected. Skipping review.")
        post_warning_comment(
            "🤖 **AI Code Review:** No substantive code changes detected in this diff. Review skipped."
        )
        return

    diff = truncate_diff(diff)

    # Step 4: Call Claude
    print("Calling Claude API...")
    try:
        feedback = call_claude(diff, rulebook)
    except Exception as e:
        print(f"Claude API error: {e}")
        post_warning_comment(
            f"⚠️ **AI Code Review:** Automated review is temporarily unavailable. Error: `{str(e)[:200]}`\n\nPlease proceed with manual review."
        )
        return

    critical_count = len(feedback.get("critical", []))
    suggested_count = len(feedback.get("suggested", []))
    print(
        f"Feedback received: {critical_count} critical, {suggested_count} suggestions"
    )

    # Step 5: Get PR files and build line position map
    print("Building line position map...")
    pr_files = get_pr_files()
    line_map = build_line_position_map(pr_files)

    # Step 6: Post comments
    print("Posting inline comments...")
    post_inline_comments(feedback, line_map)

    print("Posting summary comment...")
    post_summary_comment(critical_count, suggested_count, HEAD_SHA)

    print("Review complete.")


if __name__ == "__main__":
    main()
