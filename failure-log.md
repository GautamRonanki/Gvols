# Failure Log

## Entry 1: Claude API JSON Parse Error
**Date:** March 10, 2026

**What happened:**
Agent crashed with "automated review temporarily unavailable."
Log showed: "Invalid \escape: line 7 column 27"

**Root cause:**
Claude's response contained PHP namespaces with backslashes (e.g. App\Models).
When our code tried to parse the response as JSON, those backslashes
were treated as invalid escape characters, breaking json.loads().

**Failure type:** Infrastructure (parsing) — not an AI reasoning problem.
Claude's review was correct, our code couldn't handle the response format.

**How we fixed it:**
1. Added raw.replace("\\", "\\\\") before JSON parsing to escape backslashes
2. Wrapped json.loads() in try/except so the agent returns an empty review
   instead of crashing if future parse errors occur

**Lesson:** Always assume LLM responses will contain unexpected characters.
Build defensive parsing, not optimistic parsing.


## Entry 2: Missed Absolute Paths Rule
**Date:** March 10, 2026

**What happened:**
Agent did not flag absolute file paths in the codebase,
even though "never use absolute paths" is a critical rule in the rulebook.

**Root cause:**
The absolute path usage was in existing code, not in the changed lines
of the PR. The agent only sees the diff — it has no awareness of the
broader codebase. So it correctly reviewed what it could see, but the
problem was outside its view.

**Failure type:** Coverage failure — the agent's scope is limited to
the diff. It cannot catch issues in unchanged code that relate to
the changes being made.

**How to fix (future):**
Phase 2/3 of the QA system — RAG-based codebase context so the agent
understands how changed code connects to the rest of the repo.

**Lesson:** A review that only sees the diff is like a doctor who only
examines the body part you point at. Sometimes the problem is elsewhere.


## Entry 3: False Positives — Diff Misinterpretation
**Date:** March 10, 2026

**What happened:**
Agent flagged 3 false positives in one review:
- "Duplicate program() method" (the PR was fixing the duplication)
- "Duplicate admissionTerm() method" (same — PR was fixing it)
- "Add restrictOnDelete()" (already present in the code)

**Root cause:**
System prompt never explained diff format to Claude. Claude couldn't
distinguish between removed lines (-) and added lines (+), so it
flagged problems in old code that was actively being deleted.

**Failure type:** Prompt design failure — incomplete instructions.
Not a hallucination, not infrastructure. Claude read real data but
misinterpreted it because we didn't explain the format.

**How we fixed it:**
Added 6 lines to the system prompt explaining unified diff format
and explicitly instructing Claude to only review new code (+lines).

**Result:** Same PR went from 3 false positives to zero.

**Lesson:** Never assume the LLM understands your data format.
If you're sending structured input, explain the structure explicitly.


# Failure Diagnosis Rules

## Rule 1: Check the logs first, not the code
When something breaks, read the error message before touching anything.
Today's JSON error told us exactly what was wrong — "Invalid \escape,
line 7, column 27." The log pointed straight to the problem.

## Rule 2: Figure out WHERE in the pipeline it broke
Every failure happens at a specific stage. Ask yourself:
- Did the agent crash? (Infrastructure failure)
- Did it run but give wrong output? (Generation or prompt failure)
- Did it miss something it should have caught? (Coverage failure)
Different stages need different fixes.

## Rule 3: Look at what the LLM actually received
Don't guess what Claude saw. Print it. Today we printed the raw diff
and discovered Claude was seeing removed lines and added lines
mixed together without understanding which was which.

## Rule 4: Never assume the LLM understands your data format
If you're sending structured input (diffs, JSON, logs), explain
the format explicitly in the system prompt. Claude is smart but
it will make wrong assumptions when instructions are incomplete.

## Rule 5: Build safety nets before you need them
Wrap risky operations (like JSON parsing) in try/except so the
agent degrades gracefully instead of crashing completely.
An empty review is better than "temporarily unavailable."

## Rule 6: Verify the fix with the same input
After fixing something, run the exact same input again.
Today we ran the same PR before and after the prompt change —
went from 3 false positives to zero. That confirms the fix works.