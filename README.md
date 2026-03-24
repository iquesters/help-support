# Help Support

The `help-support` package provides an in-app documentation browser for installed Iquesters modules.

## Package Wiring

- Service provider: `Iquesters\HelpSupport\HelpSupportServiceProvider`
- view namespace: `help-support::`
- Blade pages use `@extends(app('app.layout'))`
- package configuration is managed through `HelpSupportConf`

## Routes

- `GET /help-support/{viewName}`
- `GET /help-support/docs/files/{module}`
- `GET /help-support/docs/file?url=...`

These routes are handled by `Iquesters\HelpSupport\Http\Controllers\UiController`.

## What It Does

- shows the current user only the modules they can access
- loads documentation file lists from GitHub repositories
- supports nested markdown files under the configured docs root
- enforces role-based documentation visibility
- caches both docs file lists and raw markdown content

## Current Views

- `help-support::helps.index`
- `help-support::helps.module`
- `help-support::helps.docs`

## Documentation Source

By default, docs are read from:

- repository owner: `iquesters`
- docs root path: `docs/`
- default branch fallback: `main`

These values are configurable in `HelpSupportConf`.

## Visibility Rules

- full-access roles are configured through `HelpSupportConf::docs_full_access_roles`
- default visible docs paths are configured through `HelpSupportConf::docs_default_visible_paths`

Typical default behavior:

- `super-admin` and `iq-developer` can view all docs under `docs/**`
- other users can view only `docs/users/**`

## Recommended Docs Folder Structure

Inside each module repository, organize documentation by audience first:

```text
docs/
  users/
    getting-started.md
    faq.md
    profile/
      account-settings.md

  admin/
    dashboard/
      overview.md
    module-management.md
    role-assignment.md

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

Recommended usage:

- keep end-user documentation inside `docs/users/`
- keep privileged operational docs inside `docs/admin/`
- keep technical implementation and integration docs inside `docs/developer/`
- keep cross-audience references, release notes, and shared terminology inside `docs/shared/`

This structure works well when a module has multiple documentation types, because users can immediately understand where to add new files:

- user-facing guides: `docs/users/`
- admin and support procedures: `docs/admin/`
- developer and API references: `docs/developer/`
- common references: `docs/shared/`

## Notes

- nested markdown files are supported
- raw GitHub file access is revalidated in the controller before content is returned
- see `docs/docs-access-flow.md` for the full request and caching flow
