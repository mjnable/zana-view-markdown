<?php

declare(strict_types=1);

namespace Zana\View\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Markdown to HTML with safe defaults (FE-29):
 *
 * - raw HTML in the source is escaped, never passed through;
 * - links with unsafe schemes (javascript:, vbscript:, file:, data: other
 *   than images) are removed;
 * - headings get ids, for a table of contents and links to sections;
 * - GitHub-flavoured tables, strikethrough, task lists and autolinks.
 *
 * Rendering is not free: store the HTML with the content when it is saved,
 * rather than rendering on every request.
 */
final class CommonMarkRenderer implements MarkdownRendererInterface
{
    private ?MarkdownConverter $converter = null;

    public function __construct(private readonly bool $headingAnchors = true)
    {
    }

    public function toHtml(string $markdown): string
    {
        return rtrim($this->converter()->convert($markdown)->getContent());
    }

    private function converter(): MarkdownConverter
    {
        if ($this->converter !== null) {
            return $this->converter;
        }

        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 50,
            'heading_permalink' => [
                'html_class' => 'heading-anchor',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'insert' => 'after',
                'symbol' => '#',
                'aria_hidden' => true,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        if ($this->headingAnchors) {
            $environment->addExtension(new HeadingPermalinkExtension());
        }

        return $this->converter = new MarkdownConverter($environment);
    }
}
