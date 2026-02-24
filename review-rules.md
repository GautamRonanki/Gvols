# Code Review Rulebook — Gvols
# College Programs Management System
# Stack: Laravel 11, Livewire 3, Alpine.js, Tailwind CSS, Flux UI, MySQL
# Version: 1.0

---

## Section 1: Repository Context

This repository is an admin-only College Programs Management System built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) with Flux UI components and a MySQL database. It is accessed at /admin and managed by a single authenticated admin user.

The application manages academic programs with rich structured data including taxonomies, media uploads, deadlines, concentrations, courses, faculty, and testimonials. There is no public-facing frontend. There are no API endpoints.

When reviewing code in this repository, you must apply the rules defined in this document. Do not apply generic best practices that contradict these rules.

---

## Section 2: Critical Rules

These are issues that MUST be flagged. They represent bugs, security vulnerabilities, broken logic, or patterns that should never reach production.

### Laravel General
- CRITICAL: Never use `DB::statement` or raw SQL queries without parameterized bindings. Always use Eloquent or query builder with bound parameters.
- CRITICAL: Never store uploaded file paths as full absolute paths. Store only relative paths (e.g. `programs/image.jpg`), never `/var/www/storage/...`.
- CRITICAL: Never hardcode credentials, API keys, or secrets in any file. All sensitive values must come from `.env` via `config()` or `env()`.
- CRITICAL: All routes under `/admin` must be protected by the `auth` middleware. No admin route should be accessible without authentication.
- CRITICAL: Never use `$request->all()` directly in `create()` or `update()` without mass assignment protection. Always use `$request->validated()` or explicit `$request->only([...])`.
- CRITICAL: All form requests must validate file uploads with explicit mime type and size rules (e.g. `mimes:jpg,jpeg,png,webp|max:5120`).
- CRITICAL: Never suppress exceptions with empty catch blocks. Always handle or log exceptions properly.
- CRITICAL: Avoid `dd()`, `dump()`, or `var_dump()` in any committed code. These are debug tools only.
- CRITICAL: Every Eloquent model that accepts mass assignment must define either `$fillable` or `$guarded`. Never leave both undefined.
- CRITICAL: When deleting a file from storage, always verify the file exists before attempting deletion to avoid errors.
- CRITICAL: Slug generation must produce unique slugs. Always check for uniqueness before saving.

### Livewire
- CRITICAL: Never expose sensitive model attributes through Livewire public properties. Public properties are visible in the browser's HTML source.
- CRITICAL: All Livewire actions that modify data must validate input before saving. Never save raw unvalidated data from a Livewire property.
- CRITICAL: File uploads in Livewire must use the `WithFileUploads` trait. Never handle file uploads manually in Livewire without this trait.
- CRITICAL: Never call external APIs or perform heavy database operations directly inside `render()`. Move these to separate methods or computed properties.

### Database & Migrations
- CRITICAL: Every migration that adds a foreign key must also define the `onDelete` behavior explicitly (`cascade`, `restrict`, or `set null`). Never leave foreign key behavior undefined.
- CRITICAL: Never modify an existing migration file that has already been run. Always create a new migration for changes.
- CRITICAL: Pivot table migrations must define a composite primary key or unique index on the two foreign key columns to prevent duplicate relationships.

### Security
- CRITICAL: Never trust user-supplied filenames for storage. Always use Laravel's `store()` or `storeAs()` with a generated filename, never the original filename.
- CRITICAL: Any output rendered in Blade that comes from user input must use `{{ }}` (escaped), never `{!! !!}` unless explicitly required and sanitized.
- CRITICAL: Never disable CSRF protection on any POST, PUT, PATCH, or DELETE route.

---

## Section 3: Suggested Rules

These are best practice improvements. They will not break anything but improve code quality, maintainability, and consistency.

### Laravel General
- SUGGESTED: Controllers should be thin. Business logic, data transformation, and complex queries belong in service classes or model methods, not directly in controller methods.
- SUGGESTED: Use named routes consistently. Avoid hardcoded URL strings in controllers and Blade templates.
- SUGGESTED: Eager load relationships to avoid N+1 queries. When looping through a collection and accessing a relationship, use `with()` or `load()`.
- SUGGESTED: Use `firstOrCreate`, `updateOrCreate`, and `upsert` where appropriate instead of manual check-then-insert patterns.
- SUGGESTED: Form Request classes should be used for complex validation instead of inline `$request->validate()` in controllers.
- SUGGESTED: Model relationships should always have return type declarations (e.g. `public function college(): BelongsTo`).
- SUGGESTED: Use constants or enums for fixed value sets like program format (asynchronous, synchronous, mixed, hybrid) instead of repeating raw strings.
- SUGGESTED: All `config()` values should be accessed through configuration files, not directly through `env()` outside of config files.
- SUGGESTED: Sort order fields should always be used when retrieving ordered collections (e.g. concentrations, requirements, courses).

### Livewire
- SUGGESTED: Use `#[Computed]` attributes for derived data that depends on component state rather than computing inside `render()`.
- SUGGESTED: Break large Livewire components into smaller focused components if a single component handles more than one major responsibility.
- SUGGESTED: Use `wire:key` on all repeated elements in loops to help Livewire track DOM changes correctly.
- SUGGESTED: Livewire properties that hold arrays of dynamic items (requirements, concentrations, etc.) should be initialized as empty arrays in `mount()`.
- SUGGESTED: Use `$this->dispatch()` for inter-component communication instead of directly manipulating sibling component state.

### Blade & Frontend
- SUGGESTED: Avoid inline styles in Blade templates. Use Tailwind utility classes instead.
- SUGGESTED: Extract repeated Blade markup into components (`<x-component-name>`). Do not repeat the same HTML block in multiple views.
- SUGGESTED: Use Alpine.js only for lightweight UI interactivity (toggles, dropdowns, confirmations). Do not replicate Livewire's job with Alpine.
- SUGGESTED: All form inputs should have associated `<label>` elements for accessibility.
- SUGGESTED: Flash messages and validation errors should be displayed consistently using the same partial or component across all views.

### Database
- SUGGESTED: All `string` columns that store user input should have an explicit `max` length in migrations matching the validation rules.
- SUGGESTED: Use database transactions when saving multiple related records together (e.g. saving a program and all its related concentrations, requirements, deadlines in one operation).
- SUGGESTED: Index foreign key columns in migrations if they will be frequently queried or filtered on.

---

## Section 4: What to Ignore

Do not comment on the following. These are intentional decisions for this project.

- The use of `delete-all-and-reinsert` pattern for dynamic list sections (requirements, concentrations, courses, testimonials, faculty). This is an intentional simplification for Phase 1.
- The single admin user with a simple seeded password. Authentication complexity is intentionally out of scope.
- The absence of a public frontend. This is admin-only by design.
- The absence of API endpoints or REST structure. This project does not expose an API.
- The absence of unit or feature tests. Test generation is planned for a future phase.
- CSS class ordering in Tailwind. Do not comment on the order of Tailwind utility classes.
- The use of Flux UI components. These are the approved component library for this project.
- File length or number of lines in Livewire components, as long as the component has a single clear responsibility.
- Minor code style issues like trailing whitespace, blank lines, or bracket placement.

---

## Section 5: Comment Tone

When posting feedback on Pull Requests, follow these tone guidelines:

- Be direct and specific. Reference the exact line and explain clearly what the issue is and why it matters.
- For Critical issues, explain the risk clearly (e.g. "This could allow unauthenticated access to admin routes" or "This exposes user data to XSS attacks").
- For Suggestions, frame feedback positively (e.g. "Consider using..." or "This could be improved by...").
- Never be condescending or assume the developer is careless. Frame all feedback as collaborative.
- Keep comments concise. One clear explanation per comment. Do not write essays.
- Where applicable, include a short corrected code example using GitHub's suggestion format.
- Do not repeat the same feedback multiple times across different lines. Flag it once and mention it may appear elsewhere.