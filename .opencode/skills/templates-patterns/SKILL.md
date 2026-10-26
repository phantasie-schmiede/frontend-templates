---
name: templates-patterns
description: Code pattern quick reference for the PSBits Frontend Templates extension: Fluid template skeleton with psbits namespaces and standalone ViewHelper skeleton using ViewService. Use when writing new Fluid templates or view helpers in the templates repository.
---

# Templates code patterns

Quick reference for the established patterns in this repository. Conventions (strict types,
license header, yoda conditions, alphabetical ordering, punctuation in language files) live
in `AGENTS.md`.

## Fluid template skeleton

Templates live in `Resources/Private/Templates/ViewHelpers/<Path>/<Name>.html` - never embed
HTML in PHP strings.

```html
<html
    data-namespace-typo3-fluid="true"
    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
    xmlns:templates="http://typo3.org/ns/PSBits/Templates/ViewHelpers"
    xmlns:viewHelpers="http://typo3.org/ns/PSBits/ViewHelpers/ViewHelpers">
    ...
</html>
```

## ViewHelper skeleton

View helpers are standalone: they render their own template through
`ViewService::createStandaloneView()` (ExtensionInformation + request +
`ViewHelpers/<Path>/<Name>`).

```php
<?php

declare(strict_types=1);

namespace PSBits\Templates\ViewHelpers\Component;

use PSBits\Templates\Service\ViewService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class ExampleViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function __construct(
        protected readonly ViewService $viewService,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('label', 'string', 'someone reads this');
    }

    public function render(): string
    {
        return $this->viewService->createStandaloneView(
            // ExtensionInformation + request + 'ViewHelpers/<Path>/<Name>'
        )->render();
    }
}
```
