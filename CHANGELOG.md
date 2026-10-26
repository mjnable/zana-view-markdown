# Changelog

All Zana packages share one version number and are released together. A release that did
not change this package is not listed.

## 2.3.0 — unreleased

### Changed

- Tests and tooling (`tests/`, `phpunit.xml.dist`, `phpstan.neon.dist`) are no longer in Composer
  archives, so they stay out of production `vendor/` folders (FE-17).

## 2.2.0 — 2026-10-26

First release (FE-29). Markdown to HTML with raw HTML escaped, unsafe links removed, heading
anchors and GitHub-flavoured Markdown, and the `markdown()` view helper.
