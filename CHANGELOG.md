# Release Notes

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
