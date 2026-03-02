# Help Support

The `help-support` package provides a simple UI entry point for package-scoped Blade views.

## Package Wiring

- Service provider: `Iquesters\HelpSupport\HelpSupportServiceProvider`
- View namespace: `helpsupport::`
- Base layout: `helpsupport::layouts.app`

## UI Route

The package exposes a generic UI route:

- `GET /help-support/{viewName}`

This route is handled by:

- `Iquesters\HelpSupport\Http\Controllers\UiController@show`

## View Resolution

The UI controller converts a passed view name into the package namespace.

Examples:

- Request: `/help-support/helps.index`
- Resolved view: `helpsupport::helps.index`

- Request: `/help-support/helps/create`
- Resolved view: `helpsupport::helps.create`

If the resolved view does not exist, the controller returns `404`.

## Current Views

- `helpsupport::layouts.app`
- `helpsupport::helps.index`

## Notes

- The package currently uses a dynamic view-name route for quick UI scaffolding.
- If the package grows, replace the dynamic route with explicit feature routes for better clarity and access control.
