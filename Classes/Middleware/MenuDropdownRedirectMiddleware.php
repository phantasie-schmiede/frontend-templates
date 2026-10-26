<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Middleware;

use PSBits\Templates\Data\ExtensionInformation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;

use function is_array;

/**
 * Redirect requests to menu dropdown placeholder pages to their first subpage.
 */
readonly class MenuDropdownRedirectMiddleware implements MiddlewareInterface
{
    public function __construct(
        private PageRepository $pageRepository,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $pageArguments = $request->getAttribute('routing');

        if (!$pageArguments instanceof PageArguments) {
            return $handler->handle($request);
        }

        $currentPage = $this->pageRepository->getPage_noCheck($pageArguments->getPageId());

        if (ExtensionInformation::DOKTYPES['MENU_DROPDOWN'] !== (int)($currentPage['doktype'] ?? 0)) {
            return $handler->handle($request);
        }

        $subPages     = $this->pageRepository->getMenu((int)$currentPage['uid'], 'uid');
        $firstSubPage = reset($subPages);

        if (!is_array($firstSubPage) || empty($firstSubPage['uid'])) {
            return $handler->handle($request);
        }

        $site = $request->getAttribute('site');

        if (!$site instanceof Site) {
            return $handler->handle($request);
        }

        return new RedirectResponse(
            (string)$site->getRouter()
                ->generateUri((int)$firstSubPage['uid'])
        );
    }
}
