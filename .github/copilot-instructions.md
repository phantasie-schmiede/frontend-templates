# Copilot Instructions for `psbits/templates`

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

## Safe change workflow

1. Identify the layer to change first:
    - rendering/layout → `Resources/Private/*` + TypoScript
    - custom content element schema → `ContentBlocks/.../config.yaml`
    - backend/global setting logic → `Classes/Service/GlobalVariableProviders/*`
2. Keep edits surgical and consistent with existing code style.
3. When adding Fluid-facing features, ensure required TypoScript paths/settings exist.

## Validation in this repository

- No repository-specific test/lint/build scripts were found (`composer.json` has no `scripts`, and no CI workflow files
  are present).
- Baseline validation command used:
    - `composer validate --no-check-publish`

## Onboarding run notes (errors/workarounds)

- No blocking errors occurred during onboarding.
- `composer validate` reports non-blocking warnings for `@dev` constraints on `psbits/foundation` and
  `psbits/view-helpers`; treated as existing project state.
- Because no repo test/lint/build commands are defined, the practical workaround is to run
  `composer validate --no-check-publish` plus focused file-level review for touched TYPO3/Fluid/TypoScript areas.
