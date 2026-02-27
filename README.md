# AI Code Review Agent

An AI-powered code review agent that automatically reviews pull requests 
against a defined rulebook and posts inline feedback directly on the diff.

## What it does

- Fetches the PR diff from GitHub
- Loads a repository-specific rulebook (`review-rules.md`)
- Uses Claude AI with a multi-step agent loop to analyze the code
- Fetches additional files and repo structure when it needs more context
- Posts inline comments on the exact lines with the issue and suggested fix
- Blocks merges when critical issues are found

## How it works

The agent follows a reason → act → observe loop:
1. Receives the PR diff and rulebook
2. Decides if it needs more context (fetches files or repo structure)
3. Repeats until it has enough information
4. Produces a structured review with critical issues and suggestions
5. Posts comments directly on the PR diff

## Tools available to the agent

- `fetch_file` — fetches a specific file from the repo for context
- `fetch_repo_structure` — fetches the full directory tree

## Guardrails

- Maximum 10 tool calls per review
- Diff truncated at 80,000 characters
- Maximum 5 critical issues and 5 suggestions per review
- Duplicate reviews prevented — same commit is never reviewed twice

## What this agent is NOT allowed to do

- Merge or approve pull requests
- Push any code changes to any branch
- Modify files in the repository
- Delete anything

## Error handling

- GitHub API failure → posts warning comment, exits cleanly
- Claude API failure → posts warning comment, exits cleanly  
- Missing rulebook → posts warning comment, skips review
- Trivial diff → skips review with a notice

## Cost & Performance

- Average cost per review: ~$0.03
- Average review time: ~30-60 seconds

## Setup

1. Add `review-rules.md` to your repository root
2. Add repository secrets: `ANTHROPIC_API_KEY`
3. GitHub Actions handles `GITHUB_TOKEN`, `PR_NUMBER`, `REPO_NAME`, 
   `BASE_SHA`, `HEAD_SHA` automatically

## Limitations

- Reviews diffs only — does not have full codebase awareness yet
- Large PRs are truncated — break big PRs into smaller ones
- Agent decisions are non-deterministic — same PR may get slightly 
  different feedback on re-runs
- Rulebook must be kept up to date manually