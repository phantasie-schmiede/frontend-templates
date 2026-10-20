# PSBits Templates

Instructions for AI coding agents working in this repository. Read this file at session start and follow it.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:

- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:

- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:

- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:

- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

This repository has no unit test infrastructure yet. If the goal can't be expressed as a test, express it as a
reproduction case or a lint/QA verification ("passes `composer lint:*` / `composer analyze:php`").

For multi-step tasks, state a brief plan:

```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

## Repository purpose

TYPO3 extension (`type: typo3-cms-extension`) providing page templates, Fluid ViewHelpers, TypoScript setup,
Content Blocks, and overriding templates for third-party extensions (News, Calendarize). PHP namespace:
`PSBits\\Templates\\`.

## Key locations

- `Classes/` — PHP code (ViewHelpers, services, utilities, domain models/repositories).
- `Configuration/TypoScript/` — constants/setup and includes; entrypoints are `constants.typoscript` and
  `setup.typoscript`.
- `Configuration/Sets/Template/` — the extension set definition.
- `ContentBlocks/ContentElements/*/` — Content Blocks (`config.yaml`, `templates/frontend.html`,
  `templates/backend-preview.html`, `language/{de.,}labels.xlf`).
- `Resources/Private/Templates|Partials|Layouts/` — Fluid templates.
- `Resources/Private/Extensions/` — overriding templates for News and Calendarize.
- `ext_localconf.php` — global variable provider registration.

## Architecture and conventions

- Strongly typed PHP (`declare(strict_types=1);`) is standard for classes. Every class file starts with the
  "This file is part of PSBits Templates." license header block.
- Service wiring uses Symfony DI autowiring in `Configuration/Services.yaml`.
- Global template settings are exposed via `SettingsProvider` and consumed in Fluid through
  `templates:globalVariables.settings()`. New setting sources implement `SettingSourceInterface` and are registered in
  `ext_localconf.php` via `SettingsProvider::registerSource(...)`.
- TypoScript include order matters; keep `Includes/Setup/templates.typoscript` before `page.typoscript` (as documented
  in `Configuration/TypoScript/setup.typoscript`).
- New TypoScript settings are added as `plugin.tx_templates.settings.*` constants in
  `Configuration/TypoScript/Includes/Constants/` and wired in `template.typoscript`; labels (including the constants
  labels) go into `Resources/Private/Language/Backend/Configuration/TypoScript/*.xlf`.
- Fluid templates commonly use namespaces (declare only the ones actually used):
    - `f` (`TYPO3/CMS/Fluid/ViewHelpers`)
    - `foundation` (`PSBits/Foundation/ViewHelpers`)
    - `templates` (`PSBits/Templates/ViewHelpers`)
    - `viewHelpers` (`PSBits/ViewHelpers/ViewHelpers`)
- ViewHelpers extend `AbstractViewHelper`, set `protected $escapeOutput = false;`, register arguments in
  `initializeArguments()`, render via `ViewService::createStandaloneView(...)` + an assigned Fluid template under
  `Resources/Private/Templates/ViewHelpers/<Path>/<Name>.html` — never embed HTML in PHP strings.
- Don't add empty variable initializations in Fluid templates. Fluid treats undefined variables as `null` and renders
  nothing.
- Use yoda conditions (enforced by php-cs-fixer: `yoda_style`).
- Use alphabetical ordering where possible (e.g., in arrays, attributes, imports, properties, variable declarations).
  The `lint:classes` script additionally enforces Bootstrap breakpoint ordering inside single `class` attributes:
  `xs < sm < md < lg < xl < xxl`.
- Include punctuation in language files for complete localization (colons after labels go in the `.xlf`, not in the
  template).

### Pattern quick reference

The Fluid template and ViewHelper skeletons are documented in the `templates-patterns`
skill (`.opencode/skills/templates-patterns/SKILL.md`).

## Safe change workflow

1. Identify the layer to change first:
    - rendering/layout → `Resources/Private/*` + TypoScript
    - custom content element schema → `ContentBlocks/.../config.yaml`
    - backend/global setting logic → `Classes/Service/GlobalVariableProviders/*`
2. Keep edits surgical and consistent with existing code style.
3. When adding Fluid-facing features, ensure required TypoScript paths/settings exist.

If you have questions about architecture, conventions, or best practices, ask before implementing. If you notice
inconsistencies between the codebase and these instructions, mention them - don't fix them unless asked.

## Validation

Repository quality checks are defined in `composer.json`, `package.json`, and `.github/workflows/quality.yml`.
Quality configuration files are centralized in `Build/Quality/` and referenced by Composer/NPM scripts and CI.

- `fix:all` **rewrites source files** (formats HTML/XML, reorders class attributes, runs php-cs-fixer); `lint:all` is
  the read-only verification.
- A pre-commit hook (installed by `.Build/bin/qa link` from the psbits/qa package) runs `composer lint:all`.
- Individual checks: `lint:php`, `analyze:php` (PHPStan), `lint:typoscript`, `lint:yaml`, `lint:classes`,
  `lint:htmlxml`, `lint:js`, `lint:scss`, `audit:composer`, `validate:composer`.

## Do not modify

- `Resources/Private/Scss/Bootstrap/` — vendored Bootstrap source.
- `Resources/Public/Stylesheets/Vendors/` and `Resources/Public/JavaScript/Vendors/` — compiled/generated assets.
- `node_modules/`, `.Build/`, locked files (`composer.lock`, `package-lock.json`).

## Onboarding run notes (errors/workarounds)

- Running QA locally requires installed dependencies (`composer install` / `composer update`, plus npm dependencies via
  Composer post-install).
- In restricted environments, Composer dependency installation may fail if GitHub authentication for dist downloads is
  unavailable. Running `composer lint:yaml` additionally requires `yamllint`.
