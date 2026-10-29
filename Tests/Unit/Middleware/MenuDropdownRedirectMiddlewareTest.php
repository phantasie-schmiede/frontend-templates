<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Middleware;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\Middleware\MenuDropdownRedirectMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\RouterInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class MenuDropdownRedirectMiddlewareTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Middleware
 */
class MenuDropdownRedirectMiddlewareTest extends UnitTestCase
{
    private MenuDropdownRedirectMiddleware        $subject;
    private PageRepository&MockObject             $pageRepository;
    private ServerRequestInterface&MockObject     $request;
    private RequestHandlerInterface&MockObject    $handler;
    private Response                              $response;

    /** @var array<string, mixed> */
    private array                                 $requestAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageRepository = $this->createMock(PageRepository::class);
        $this->subject        = new MenuDropdownRedirectMiddleware($this->pageRepository);
        $this->request        = $this->createMock(ServerRequestInterface::class);
        $this->handler        = $this->createMock(RequestHandlerInterface::class);
        $this->response       = new Response();

        $this->handler->method('handle')
            ->willReturn($this->response);
        $this->request->method('getAttribute')
            ->willReturnCallback(function (string $name, mixed $default = null) {
                return $this->requestAttributes[$name] ?? $default;
            });
    }

    #[Test]
    public function requestsWithoutRoutingAttributesArePassedThrough(): void
    {
        self::assertSame($this->response, $this->subject->process($this->request, $this->handler));
    }

    #[Test]
    public function regularPagesArePassedThrough(): void
    {
        $this->provideRoutingPage(['doktype' => 1, 'uid' => 1]);

        self::assertSame($this->response, $this->subject->process($this->request, $this->handler));
    }

    #[Test]
    public function menuDropdownPagesWithoutSubPagesArePassedThrough(): void
    {
        $this->provideRoutingPage(['doktype' => 100, 'uid' => 1]);
        $this->pageRepository->method('getMenu')
            ->with(1, 'uid')
            ->willReturn([]);

        self::assertSame($this->response, $this->subject->process($this->request, $this->handler));
    }

    #[Test]
    public function menuDropdownPagesWithoutAnUsableSubPageArePassedThrough(): void
    {
        $this->provideRoutingPage(['doktype' => 100, 'uid' => 1]);
        $this->pageRepository->method('getMenu')
            ->with(1, 'uid')
            ->willReturn([['uid' => 0]]);

        self::assertSame($this->response, $this->subject->process($this->request, $this->handler));
    }

    #[Test]
    public function menuDropdownPagesWithoutASiteArePassedThrough(): void
    {
        $this->provideRoutingPage(['doktype' => 100, 'uid' => 1]);
        $this->pageRepository->method('getMenu')
            ->with(1, 'uid')
            ->willReturn([['uid' => 42]]);

        self::assertSame($this->response, $this->subject->process($this->request, $this->handler));
    }

    #[Test]
    public function menuDropdownPagesRedirectToTheirFirstSubPage(): void
    {
        $this->provideRoutingPage(['doktype' => 100, 'uid' => 1]);
        $this->pageRepository->method('getMenu')
            ->with(1, 'uid')
            ->willReturn([['uid' => 42], ['uid' => 43]]);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generateUri')
            ->with(42)
            ->willReturn(new Uri('https://example.com/sub-page'));

        $site = $this->createMock(Site::class);
        $site->method('getRouter')
            ->willReturn($router);
        $this->requestAttributes['site'] = $site;

        $response = $this->subject->process($this->request, $this->handler);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('https://example.com/sub-page', $response->getHeaderLine('Location'));
    }

    /**
     * @param array<string, mixed> $page
     */
    private function provideRoutingPage(array $page): void
    {
        $pageArguments = $this->createMock(PageArguments::class);
        $pageArguments->method('getPageId')
            ->willReturn(1);

        $this->requestAttributes['routing'] = $pageArguments;
        $this->pageRepository->method('getPage_noCheck')
            ->with(1)
            ->willReturn($page);
    }
}
