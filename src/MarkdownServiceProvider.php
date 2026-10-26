<?php

declare(strict_types=1);

namespace Zana\View\Markdown;

use Zana\Configuration\ConfigurationInterface;
use Zana\Container\Contracts\ContainerInterface;
use Zana\Container\Contracts\ServiceCollectionInterface;
use Zana\Container\Contracts\ServiceResolverInterface;
use Zana\Core\ServiceProvider;
use Zana\View\Markup;
use Zana\View\ViewHelpers;

/**
 * Registers the Markdown renderer and the markdown() view helper, which
 * prints safe HTML: <?= $this->markdown($post->body) ?>, or
 * {{ markdown(post.body) }} with Twig. Reads view.markdown.heading_anchors.
 */
final class MarkdownServiceProvider extends ServiceProvider
{
    public function register(ServiceCollectionInterface $services): void
    {
        $services
            ->singleton(CommonMarkRenderer::class, static fn (ServiceResolverInterface $r): CommonMarkRenderer => new CommonMarkRenderer(
                $r->get(ConfigurationInterface::class)->bool('view.markdown.heading_anchors', true),
            ))->uses(ConfigurationInterface::class)
            ->alias(MarkdownRendererInterface::class, CommonMarkRenderer::class);
    }

    public function boot(ContainerInterface $container): void
    {
        $helpers = $container->get(ViewHelpers::class);
        $markdown = $container->get(MarkdownRendererInterface::class);
        assert($helpers instanceof ViewHelpers && $markdown instanceof MarkdownRendererInterface);

        $helpers->add('markdown', static fn (?string $source): Markup => new Markup($markdown->toHtml((string) $source)), safe: true);
    }

    public function origin(): string
    {
        return 'package:mj-zana/view-markdown';
    }
}
