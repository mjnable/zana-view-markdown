<?php

declare(strict_types=1);

namespace Zana\View\Markdown\Tests;

use PHPUnit\Framework\TestCase;
use Zana\Core\Application;
use Zana\Http\HttpServiceProvider;
use Zana\Routing\RoutingServiceProvider;
use Zana\View\Markdown\CommonMarkRenderer;
use Zana\View\Markdown\MarkdownServiceProvider;
use Zana\View\ViewHelpers;
use Zana\View\ViewServiceProvider;

final class MarkdownTest extends TestCase
{
    public function testMarkdownBecomesHtmlWithAnchors(): void
    {
        $html = (new CommonMarkRenderer())->toHtml("## Our services\n\nWe build **websites** and [apps](https://acme.example).\n\n| Plan | Price |\n|---|---|\n| Basic | 10 |");

        $this->assertMatchesRegularExpression('{<h2>Our services<a id="our-services" href="#our-services" class="heading-anchor"[^>]*>#</a></h2>}', $html);
        $this->assertStringContainsString('<strong>websites</strong>', $html);
        $this->assertStringContainsString('<a href="https://acme.example">apps</a>', $html);
        $this->assertStringContainsString('<table>', $html);
    }

    public function testRawHtmlIsEscapedAndUnsafeLinksRemoved(): void
    {
        $html = (new CommonMarkRenderer(headingAnchors: false))->toHtml("<script>alert(1)</script>\n\n<img src=x onerror=alert(1)>\n\n[click](javascript:alert(1)) [file](file:///etc/passwd)");

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('file:', $html);
    }

    public function testTheViewHelperPrintsSafeHtml(): void
    {
        $helpers = Application::configure(sys_get_temp_dir() . '/zana-view-markdown')
            ->withEnvironment('testing')
            ->withProviders(HttpServiceProvider::class, RoutingServiceProvider::class, ViewServiceProvider::class, MarkdownServiceProvider::class)
            ->build()
            ->container()
            ->get(ViewHelpers::class);
        assert($helpers instanceof ViewHelpers);

        $this->assertTrue($helpers->isSafe('markdown'));
        $this->assertSame('<p><em>Hello</em></p>', (string) $helpers->call('markdown', ['*Hello*']));
        $this->assertSame('', (string) $helpers->call('markdown', [null]));
    }
}
