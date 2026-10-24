---
name: PSBits Templates
description: psbits/templates — Bootstrap/Content-Blocks template pack for TYPO3. The only psbits extension with page types (menuDropdown), v14 status after the foundation IconRegistry fix, site set and ViewHelpers overview. Use when working on templates or its v13/v14 behavior.
---

# psbits/templates

"Basic templates for TYPO3": page layouts, content elements (Content Blocks),
Fluid ViewHelpers, and TypoScript setup built on Bootstrap. Detailed docs
live in the repo's `README.md` (content elements, backend layouts, TypoScript
settings, ViewHelpers, extensibility) — reference it, do not duplicate it.

## Position in the stack

- Depends on `psbits/foundation ^4.2`, `friendsoftypo3/content-blocks
  ~1.5 || ^2.4`, `psbits/view-helpers`, `wapplersystems/ws-scss`,
  `typo3/cms-fluid-styled-content`; core constraint `^13.4 || ^14.3`.
- Integration via site set (site config `dependencies: - psbits/templates`).

## The page type (important)

- `Classes/Data/ExtensionInformation.php` registers **one page type:
  `menuDropdown`** (`DOKTYPES['MENU_DROPDOWN']`) — templates is the **only
  psbits extension that defines page types**.
- That made it the only queue extension affected by the v14
  IconRegistry-at-boot failure in Foundation's `PageTypeService` (see the
  `typo3-v14-migration` skill in the ddev-typo3 project for the mechanism).
- **v14 status: working** — verified end-to-end in the cb-repro scratch
  project (core v14.3.10 + content-blocks 2.4.10 + templates + foundation
  `4.2.x` with the fix `3d2d3f9`): `composer update` → `asset:publish`
  done, fresh build and cache run.

## Tests

- `test:all` is unit tests only (no functional suite) — v14 boot coverage for
  this extension comes from scratch-project runs and the shared ddev site.

## See also

- `psbits-extension-dev` (conventions), `typo3-v14-migration` (the v14
  pitfall) — both in the ddev-typo3 project.
- `vendor/psbits/foundation/.opencode/skills/foundation/SKILL.md` (the fix
  this extension depends on).
