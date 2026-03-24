# Help Support Docs Access Flow

## Overview

The help-support package loads documentation files from GitHub repositories under the `iquesters/*` organization and applies role-based visibility before returning the file list to the UI.

## Current Rules

- full-access roles come from `HelpSupportConf::docs_full_access_roles`
- default visible paths come from `HelpSupportConf::docs_default_visible_paths`
- repository owner, docs root path, and default branch are configurable in `HelpSupportConf`

## Recommended Repository Structure

Each documented module should keep docs under the configured root path and split them by audience:

```text
docs/
  users/
    getting-started.md
    faq.md
    profile/
      notifications.md

  admin/
    operations.md
    access-control.md
    troubleshooting/
      common-issues.md

  developer/
    architecture.md
    api/
      authentication.md
      webhooks.md
    deployment/
      environment.md

  shared/
    glossary.md
    release-notes.md
```

Recommended intent:

- `docs/users/` for end-user-safe documentation
- `docs/admin/` for privileged operational and support documentation
- `docs/developer/` for technical, API, architecture, and deployment documentation
- `docs/shared/` for docs that multiple audiences may need

When a module has multiple doc types, prefer creating a new file inside the correct audience folder instead of mixing everything in the docs root.

## Request Flow

1. The module listing page opens the docs page for a selected module.
2. The docs page requests `GET /help-support/docs/files/{module}`.
3. `UiController@getModuleDocs` fetches the repository metadata from GitHub.
4. The controller fetches the repository tree recursively from GitHub and filters markdown files inside `docs/**`.
5. The controller applies role-based path filtering before returning the file list.
6. When a user opens a file, the docs page requests `GET /help-support/docs/file?url=...`.
7. `UiController@getDocFile` validates the GitHub raw URL, re-checks path visibility, fetches the markdown, caches it, and returns plain text.

## Caching

- The docs list is cached per module and visibility scope.
- Raw markdown file contents are cached by file URL.
- Cache duration is controlled by `HelpSupportConf::docs_cache_hours`.
