# Release Notes

## 0.3.0 - 2026-11-08

### Added

- Papers can have named pages, such as `/papers/orders/done`. Add an optional parameter to `sections()`, which gets the page name from the URL: `sections(?string $page = null)`. Papers without it have no named pages, so their URLs with a page name are not found.
- Papers can have named actions, such as `POST /papers/orders/refund`. The action name is passed to the first parameter of `action()` which isn't a dependency, whatever its name: `action(Request $request, string $action)`. Papers without such a parameter have no named actions.
- Papers make the URLs of their pages and actions: `static::url('history')`, `static::actionUrl('refund')` and `static::redirect('done')`. They take query parameters too: `static::url(query: ['status' => 'open'])`.
- `FormContent` makes forms with a CSRF token, whose fields show the old input and the errors of a failed validation.
- `ButtonContent` makes a button submitting an action on its own, e.g. in a table row, with hidden values.
- `LinkContent` makes links, which may look like buttons.
- `AlertContent` highlights success, error, warning or info messages, such as the result of an action.
- Forms and buttons can ask the user to confirm them before they are submitted.

### Changed

- The routes `papers.show` and `papers.action` have an optional `page` and `action` parameter. The papers API routes are now registered before them, so a paper URL with a name never takes the place of an API route.
- Papers defining static `url()`, `actionUrl()` or `redirect()` methods of their own have to rename them.

If you published the views, publish the new `components/form.blade.php`, `components/button.blade.php`, `components/link.blade.php` and `components/alert.blade.php`.

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
