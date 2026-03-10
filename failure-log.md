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


## Entry 3: False Positives — Hallucination
**Date:** March 10, 2026

**What happened:**
1. Agent flagged a "duplicate program() method" as critical.
   Only one program() method exists. No duplicate.
2. Agent suggested adding restrictOnDelete() to foreign keys
   that already have restrictOnDelete() defined.

**Root cause:**
Claude hallucinated in both cases. It had the code in front of it
and still misread it — seeing problems that don't exist.

**Failure type:** Generation failure (hallucination / false positive).
Input was correct, pipeline worked, Claude just got it wrong. Twice.

**How to fix:**
No clear fix yet — LLM reasoning errors, not code bugs.

**Lesson:** False positives are trust killers. If a developer gets
two wrong flags in one review, they stop reading the agent's feedback.
Quality matters more than quantity.