<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Functional\Fixtures;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;

/**
 * Class FunctionalRequestFactory
 *
 * Creates frontend requests with an initialized FrontendTypoScript, as the frontend request
 * middlewares would. The framework configuration of the extension under test is provided under
 * its plugin key, which is derived from the extension name.
 *
 * @package PSBits\FrontendTemplates\Tests\Functional\Fixtures
 */
final class FunctionalRequestFactory
{
    /**
     * @param array<string, mixed> $requestAttributes
     */
    public static function createFrontendRequest(array $requestAttributes = []): ServerRequest
    {
        $frontendTypoScript = new FrontendTypoScript(new RootNode(), [], [], []);
        $frontendTypoScript->setSetupArray([
            'plugin.' => [
                'tx_frontendtemplates.' => [
                    'view.' => [],
                ],
            ],
        ]);

        $request = (new ServerRequest(
            'http://example.com/',
            'GET',
            null,
            [],
            [
                'HTTP_HOST'   => 'example.com',
                'REQUEST_URI' => '/',
            ]
        ))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', new SiteLanguage(0, 'en', new Uri('/'), ['title' => 'Default language']))
            ->withAttribute('frontend.typoscript', $frontendTypoScript);

        foreach ($requestAttributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        return $request;
    }
}
