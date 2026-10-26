<?php

declare(strict_types=1);

namespace Zana\View\Markdown;

interface MarkdownRendererInterface
{
    /**
     * Safe HTML for Markdown written by editors.
     */
    public function toHtml(string $markdown): string;
}
