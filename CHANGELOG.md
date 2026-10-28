# Release Notes

## 0.2.3 - 2026-10-28

### Added

- Papers pages can have a favicon of their own, so their browser tabs stand out from the rest of your app: `Papers::favicon(asset('favicon-green.png'));`.
- Any other HTML can be added to the head of papers pages, such as more icons, a web manifest or a theme color: `Papers::head(fn () => view('papers.head'));`. It is not escaped, and a closure is called whenever a page is rendered.

If you published the views, update `app.blade.php`.

## 0.2.2 - 2026-10-28

### Added

- A filter for finding papers in the navigation and on the index page. It searches the name, description, URL handler and keywords of papers, ignores letter case and diacritics, and ranks matches in the name first. Press `/` to focus it, the arrow keys and `Enter` to open a paper, and `Escape` to clear it. The query is kept while browsing papers in the same browser tab.
- Papers can define keywords to be found by other words too, such as synonyms or translations of their name: `public static $keywords = ['revenue', 'tržby'];`.

### Fixed

- The menu script no longer throws when no papers are registered.

If you published the views, update `pages/index.blade.php` and `partials/_nav.blade.php`, and publish the new `components/filter.blade.php`.

## 0.2.1 - 2026-10-28

### Added

- Papers can use the full width of the screen. Set `public static $fullWidth = true;` on a paper to remove the width limit of its page.
- Tables can be fitted to the width of their section with `TableContent::fit()`. The table spans the whole section and its columns shrink by wrapping their content instead of scrolling horizontally. Headers wrap between words only, while values may break anywhere, so long URLs or references don't widen the table.

If you published the views, update `pages/paper.blade.php` and `components/table.blade.php`.

## 0.2.0 - 2026-10-28

### Added

- Papers can handle actions. Define an optional `action()` method on a paper and submit forms from its sections to the paper's URL with the `POST` method (route `papers.action`). The request runs in the `web` middleware group, so it is protected against CSRF.
- Dependencies of the `action()` method are resolved from the container, including form requests.
- A response returned by `action()` is sent as it is. When it returns nothing, the paper page is rendered by the same paper instance, so its sections can show the result of the action.
- Papers without an `action()` method answer `POST` requests with `405 Method Not Allowed`.

### Changed

- The current paper stays highlighted in the navigation on a page rendered after an action. If you published the views, update `partials/_nav.blade.php`.

Sections are built for every `GET` request, which is not protected against CSRF. Papers which change data in `sections()` should move that code to `action()`.

## 0.1.0 - 2025-05-28

initial release

## v0.1.0 (202x-xx-xx)

Initial pre-release.
