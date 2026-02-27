import os
import json
import time
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
    lines = [l for l in diff.splitlines() if l.startswith("+") or l.startswith("-")]
    substantive = [l for l in lines if l.strip() not in ("+", "-", "+++", "---")]
    return len(substantive) == 0


def truncate_diff(diff: str, max_chars: int = 80000) -> str:
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

    def dispatch_tool(name: str, args: dict) -> str:
        if name == "fetch_file":
            return fetch_file(**args)
        elif name == "fetch_repo_structure":
            return fetch_repo_structure(**args)
        else:
            return f"Unknown tool: {name}"

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
Do not write any text before or after the JSON. Output the JSON object immediately."""

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

        messages.append({"role": "assistant", "content": response.content})

        if response.stop_reason == "end_turn":
            for block in response.content:
                if hasattr(block, "text"):
                    raw = block.text.strip()
                    if "```" in raw:
                        raw = raw.split("```")[1]
                        if raw.startswith("json"):
                            raw = raw[4:]
                    start = raw.find("{")
                    end = raw.rfind("}") + 1
                    if start != -1 and end != 0:
                        raw = raw[start:end]

                    # Log cost before returning
                    usage = response.usage
                    input_tokens = usage.input_tokens
                    output_tokens = usage.output_tokens
                    cost = (input_tokens / 1_000_000 * 3) + (
                        output_tokens / 1_000_000 * 15
                    )
                    print(
                        f"💰 Tokens: {input_tokens} in, {output_tokens} out — estimated cost: ${cost:.4f}"
                    )

                    return json.loads(raw)

        elif response.stop_reason == "tool_use":
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

    raise Exception("Agent loop ended without producing a review.")


# ── Step 5: Get PR files for line mapping ─────────────────────────────────────
def get_pr_files():
    url = f"{GITHUB_API}/repos/{REPO_NAME}/pulls/{PR_NUMBER}/files"
    response = requests.get(url, headers=GITHUB_HEADERS)
    return response.json()


def build_line_position_map(pr_files):
    mapping = {}
    for f in pr_files:
        filename = f["filename"]
        patch = f.get("patch", "")
        if not patch:
            continue

        mapping[filename] = {}
        position = 0
        new_line = 0

        for patch_line in patch.splitlines():
            position += 1

            if patch_line.startswith("@@"):
                try:
                    new_part = patch_line.split("+")[1].split("@@")[0].strip()
                    new_line = int(new_part.split(",")[0]) - 1
                except (IndexError, ValueError):
                    new_line = 0

            elif patch_line.startswith("-"):
                pass

            elif patch_line.startswith("\\"):
                position -= 1

            else:
                new_line += 1
                mapping[filename][new_line] = position

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
    comments = []
    valid_files = set(line_map.keys())

    for item in feedback.get("critical", []):
        file = item.get("file", "")
        line = item.get("line", 0)
        comment_text = item.get("comment", "")
        suggestion = item.get("suggestion", "")

        if file not in valid_files:
            print(f"⚠️  Skipping {file} — not in diff")
            continue

        position = line_map.get(file, {}).get(line)
        if not position:
            print(f"⚠️  Skipping {file}:{line} — no position found")
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

        if file not in valid_files:
            print(f"⚠️  Skipping {file} — not in diff")
            continue

        position = line_map.get(file, {}).get(line)
        if not position:
            print(f"⚠️  Skipping {file}:{line} — no position found")
            continue

        body = f"🟡 **Suggestion**\n\n{comment_text}"
        if suggestion:
            body += f"\n\n```suggestion\n{suggestion}\n```"

        comments.append({"path": file, "position": position, "body": body})

    if not comments:
        print("No valid inline comments to post.")
        return

    url = f"{GITHUB_API}/repos/{REPO_NAME}/pulls/{PR_NUMBER}/reviews"
    posted = 0
    for comment in comments:
        payload = {"commit_id": HEAD_SHA, "event": "COMMENT", "comments": [comment]}
        response = requests.post(url, headers=GITHUB_HEADERS, json=payload)
        if response.status_code in (200, 201):
            posted += 1
            print(f"✅ Posted comment on {comment['path']}:{comment['position']}")
        else:
            print(
                f"⚠️  Failed {comment['path']}:{comment['position']} — {response.status_code}"
            )

    print(f"Posted {posted}/{len(comments)} inline comments.")


def post_warning_comment(message: str):
    url = f"{GITHUB_API}/repos/{REPO_NAME}/issues/{PR_NUMBER}/comments"
    requests.post(url, headers=GITHUB_HEADERS, json={"body": message})


# ── Main ──────────────────────────────────────────────────────────────────────
def main():
    start_time = time.time()
    print("Starting AI Code Review Agent...")

    print("Loading rulebook...")
    rulebook = load_rulebook()

    print("Checking previously reviewed commits...")
    try:
        reviewed_commits = get_reviewed_commits()
        if HEAD_SHA in reviewed_commits:
            print(f"Commit {HEAD_SHA[:7]} already reviewed. Skipping.")
            return
    except Exception as e:
        print(f"Warning: Could not check reviewed commits: {e}. Proceeding anyway.")

    print("Fetching diff...")
    try:
        diff = get_diff()
    except Exception as e:
        post_warning_comment(
            f"⚠️ **AI Code Review:** Could not fetch PR diff. Error: `{str(e)[:200]}`\n\nPlease proceed with manual review."
        )
        return

    if is_trivial_diff(diff):
        print("Trivial diff detected. Skipping review.")
        post_warning_comment(
            "🤖 **AI Code Review:** No substantive code changes detected. Review skipped."
        )
        return

    diff = truncate_diff(diff)

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

    print("Building line position map...")
    try:
        pr_files = get_pr_files()
        line_map = build_line_position_map(pr_files)
    except Exception as e:
        print(f"Warning: Could not build line map: {e}. Posting summary only.")
        line_map = {}

    print("Posting inline comments...")
    post_inline_comments(feedback, line_map)

    print("Posting summary comment...")
    try:
        post_summary_comment(critical_count, suggested_count, HEAD_SHA)
    except Exception as e:
        print(f"Warning: Could not post summary comment: {e}")

    elapsed = time.time() - start_time
    print(f"⏱️  Total review time: {elapsed:.1f}s")
    print("Review complete.")

    if critical_count > 0:
        print(
            f"Exiting with code 1 — {critical_count} critical issue(s) found. Merge is blocked."
        )
        exit(1)


if __name__ == "__main__":
    main()
