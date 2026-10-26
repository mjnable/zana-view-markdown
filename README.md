# mj-zana/view-markdown

Safe Markdown for [mj-zana/view](https://github.com/mjnable/zana-view), on
`league/commonmark`.

```
composer require mj-zana/view-markdown
```

Register `MarkdownServiceProvider` after `ViewServiceProvider`. Templates
then print editors' Markdown with `<?= $this->markdown($post->body) ?>`
(Twig: `{{ markdown(post.body) }}`).

- **Raw HTML is escaped**, never passed through, so editors cannot inject
  scripts.
- **Unsafe links are removed:** `javascript:`, `vbscript:`, `file:`, and
  `data:` other than images.
- **Headings get ids** for links to sections (`view.markdown.heading_anchors`).
- **GitHub-flavoured Markdown:** tables, strikethrough, task lists and
  autolinks.
- **Rendering costs time:** store the HTML with the content when it is
  saved, through `MarkdownRendererInterface`, instead of rendering on every
  request.

## License

MIT
