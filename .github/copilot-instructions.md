# Copilot Instructions for `psbits/templates`

If you haven't already, please read the general agent instructions in `../AGENTS.md` before proceeding with this file.

## Repository purpose

- TYPO3 v13 extension (`type: typo3-cms-extension`) providing page templates, Fluid ViewHelpers, TypoScript setup, and
  Content Blocks.
- PHP namespace: `PSBits\\Templates\\`.

## Key locations

- `Classes/` — PHP code (ViewHelpers, services, utilities, domain models/repositories).
- `Configuration/TypoScript/` — constants/setup and includes; entrypoints are `constants.typoscript` and
  `setup.typoscript`.
- `ContentBlocks/ContentElements/*/` — Content Blocks (`config.yaml`, frontend/backend templates, labels).
- `Resources/Private/Templates|Partials|Layouts/` — Fluid templates.
- `ext_localconf.php` — global variable provider registration.
- `ext_tables.sql` — DB extension for `pages` (`tx_templates_bs_container_type`).

## Architecture and conventions

- Strongly typed PHP (`declare(strict_types=1);`) is standard for classes.
- Service wiring uses Symfony DI autowiring in `Configuration/Services.yaml`.
- Global template settings are exposed via `SettingsProvider` and consumed in Fluid through
  `templates:globalVariables.settings()`.
- TypoScript include order matters; keep `Includes/Setup/templates.typoscript` before `page.typoscript` (as documented
  in `Configuration/TypoScript/setup.typoscript`).
- Fluid templates commonly use namespaces:
    - `f` (`TYPO3/CMS/Fluid/ViewHelpers`)
    - `foundation` (`PSBits/Foundation/ViewHelpers`)
    - `templates` (`PSBits/Templates/ViewHelpers`)
- Use yoda conditions for better readability and to avoid accidental assignment.
- Use alphabetical ordering where possible (e.g., in arrays, attributes and imports) for consistency.
- Include punctuation in language files for complete localization, e.g. prepended colons should not be placed in the
  template after a label, but in the language file itself.

If you have questions about architecture, conventions, or best practices, ask before implementing. If you notice any
inconsistencies in the codebase or differences between codebase and these instructions, mention them - don't fix them
unless asked.

## Safe change workflow

1. Identify the layer to change first:
    - rendering/layout → `Resources/Private/*` + TypoScript
    - custom content element schema → `ContentBlocks/.../config.yaml`
    - backend/global setting logic → `Classes/Service/GlobalVariableProviders/*`
2. Keep edits surgical and consistent with existing code style.
3. When adding Fluid-facing features, ensure required TypoScript paths/settings exist.

## Validation in this repository

- Repository quality checks are defined in `composer.json`, `package.json`, and `.github/workflows/quality.yml`.
- Primary local QA command:
    - `composer qa`
    - Runs: `audit:composer`, `validate:composer`, `lint:php`, `analyze:php`, `lint:typoscript`, `lint:classes`,
      `lint:js`, `lint:scss`
- Additional lint command used in CI:
    - `composer lint:yaml`

## Onboarding run notes (errors/workarounds)

- Running QA locally requires installed dependencies (`composer install` / `composer update`, plus npm dependencies via
  Composer post-install).
- In restricted environments, Composer dependency installation may fail if GitHub authentication for dist downloads is
  unavailable.
