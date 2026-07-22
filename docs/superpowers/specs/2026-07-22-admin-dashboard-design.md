# Lyn's Little Kitchen Admin Dashboard Design

**Date:** 2026-07-22  
**Status:** Approved design direction  
**Scope:** Functional core admin screens with branded placeholders for future modules

## Purpose

Build a secure Nuxt 4 and Vue 3 administration area that closely follows the supplied reference: a warm, compact bakery dashboard with a fixed desktop sidebar, responsive mobile drawer, data-rich cards and tables, and a dedicated split-panel login screen.

Laravel remains the source of truth for authentication, role authority, validation, products, inventory, orders, and dashboard data. Nuxt is responsible for presentation, session mediation, navigation, and user-friendly error handling.

## Functional scope

The first release contains fully functional screens for:

- Admin login and logout
- Dashboard
- Product list
- Product creation and editing
- Product deletion through a confirmation dialog
- Order list
- Order detail and valid status changes
- Browser print invoice
- Responsive desktop and mobile admin navigation

The following routes remain visible in navigation but open intentional, branded placeholder pages:

- Customers
- Delivery zones
- Promotions
- Reports
- Settings

Each placeholder explains that the module is not connected yet and provides navigation back to the dashboard. It does not display fabricated analytics or management controls.

## Visual direction

The admin interface recreates the reference's restrained bakery aesthetic while sharing the existing storefront identity.

### Palette

- Porcelain: `#FBF7F2` — page background
- Paper: `#FFFDF9` — panels, tables, and forms
- Cocoa: `#3D241B` — primary text
- Terracotta: `#9D523B` — primary actions and emphasis
- Peach wash: `#F2DFD4` — active navigation and soft surfaces
- Sage: `#5F7D63` — paid, active, and positive states
- Amber: `#B97835` — preparing and low-stock warnings
- Rose: `#B85A4D` — destructive and rejected states

### Typography

- The brand mark and important editorial headings use the existing serif family.
- Navigation, forms, tables, and data use the existing system sans-serif family for compact readability.
- Monetary totals use tabular numerals where supported.

### Layout signature

The distinctive element is a quiet "bakery ledger" system: thin cocoa rules, small status stamps, compact monetary figures, and parchment-toned panels. The interface remains operational rather than decorative.

The login screen uses a split card: credentials and brand copy on the left, with one of the existing temporary cookie photographs filling the right. On narrow screens the photograph becomes a shallow banner so the form remains the primary task.

The desktop application uses a narrow fixed sidebar and a scrollable content region. On mobile, the sidebar becomes an off-canvas drawer over a dimmed dashboard, matching the supplied reference.

## Information architecture

### Authentication

- `/admin/login`

### Functional routes

- `/admin` — dashboard
- `/admin/products` — product list
- `/admin/products/new` — create product
- `/admin/products/[id]/edit` — edit product
- `/admin/orders` — order list
- `/admin/orders/[id]` — order detail
- `/admin/orders/[id]/print` — print-focused invoice view

### Placeholder routes

- `/admin/customers`
- `/admin/delivery-zones`
- `/admin/promotions`
- `/admin/reports`
- `/admin/settings`

## Authentication and authority

Laravel's `auth:sanctum` and `admin` middleware remain the final security boundary. Hiding links or redirecting inside Vue is not considered authorization.

The browser sends credentials to a Nuxt server login handler. That handler calls Laravel's `/api/login`, verifies the returned user has the `admin` role, and stores the Sanctum token in a `HttpOnly`, `SameSite=Lax` cookie. The cookie is `Secure` in production. The raw token is never placed in Pinia, `localStorage`, rendered HTML, or client JavaScript.

Explicit Nuxt server handlers forward only supported admin requests to Laravel and attach the bearer token. No open-ended URL proxy is created. A failed customer-role login revokes the issued Laravel token before returning an access-denied response.

Session behavior:

- `401` clears the local session and redirects to `/admin/login`.
- `403` renders an access-denied state without pretending the resource is missing.
- Logout revokes the current Laravel token and clears the cookie.
- A successful login redirects to `/admin` or to the original protected destination.

The implementation connects to the backend's current `/api` prefix. API version migration is outside this visual admin release.

## Backend contract additions

The improved backend already supports admin product listing, dashboard summaries, protected order listing, and valid order-status transitions. The following minimal additions are required for direct, refresh-safe admin screens:

- `GET /api/admin/products/{product}` for product editing by ID
- `GET /api/admin/orders/{order}` for order details
- An admin-only order resource containing guest or customer name, phone, and email
- Dashboard order-status counts and recent-order summaries, or equivalent protected queries that provide accurate data without counting only the first paginated page

The public order-tracking response must not gain private contact fields. Protected fulfilment data belongs in an admin-specific resource.

The existing API accepts product image URLs rather than uploaded files. The product form therefore provides repeatable URL fields, ordering, preview, and removal. It does not show a non-functional upload control. Cloudinary upload remains a later feature.

## Screen designs

### Login

The login card contains the Lyn's brand, “Admin Area” heading, short purpose statement, email, password with visibility control, optional remember-session preference, and a single Sign in action. Validation errors remain adjacent to their fields. The password-recovery link is omitted until a recovery API exists.

### Dashboard

The dashboard contains:

- Today's paid sales
- Total orders
- Monthly paid revenue
- Best-selling paid product
- Order-status overview
- Low-stock product and variant alerts
- Recent orders
- Quick actions for a new product and pending orders

No trend percentage or chart is shown unless the API supplies comparative or time-series values. Missing metrics use honest empty states.

### Products

The product list contains search, category and status filters, pagination, product image and name, category, variant count, stock summary, status, edit, and delete actions. Mobile layouts become stacked product rows rather than compressing an unreadable desktop table.

Create and edit use one shared form with Basic info, Variants, and Images sections. The SEO tab shown in the reference is omitted because the current product contract has no separate SEO fields. Validation maps Laravel `422` errors to the relevant controls.

Products with variants show variant stock as the operational inventory. Base stock is clearly labelled as unused when variants exist.

### Orders

The order list contains reference search, payment status, order status, delivery method, pagination, and an action leading to the detail screen.

The detail screen contains customer contact information, fulfilment information, line items, totals, payment status, notes, and status controls. The UI only offers values returned in `allowed_next_statuses`, while Laravel independently enforces the same transitions.

Payment and fulfilment remain separate concepts. Payment status is displayed as a badge. The order timeline follows:

`pending → preparing → baking → packing → out_for_delivery → completed`

`rejected` and `cancelled` are terminal side states and do not appear as successful progress steps.

### Print invoice

The print route uses semantic HTML and print CSS, not a fabricated PDF preview. It contains the business identity, order reference, order and payment dates when available, billed customer, delivery details, item table, subtotal, delivery fee, and total. Navigation, controls, backgrounds, and shadows are removed during printing. The browser's native print dialog can save the result as PDF.

### Placeholder modules

Each future module shares one reusable placeholder component with a module-specific title and explanation. Navigation remains consistent, but calls to unavailable APIs are never made.

## State and component boundaries

- An admin-auth store contains only the safe user and session state.
- Admin API composables own request parameters, response typing, and normalized errors.
- Filters are reflected in route query parameters so lists survive refresh and are shareable.
- Product form state is local to the shared product editor.
- Order status updates invalidate or refresh both order detail and dashboard-derived data.
- Currency, dates, stock, and status labels use shared formatter utilities.
- The storefront cart and catalogue stores remain independent from all admin state.

## Responsive behavior

- Desktop: persistent narrow sidebar with a wide content workspace.
- Tablet: narrower sidebar and horizontally scrollable tables only where stacked rows would lose meaning.
- Mobile: off-canvas navigation, stacked metrics, card-based list rows, full-width form actions, and a compact sticky page header.
- Interactive targets remain at least approximately 44 pixels.
- Focus indicators, reduced-motion preferences, field labels, dialog focus handling, and meaningful status text are required.

## Errors and empty states

Every functional page handles loading, empty, offline/backend unavailable, `401`, `403`, `404`, `422`, `429`, and unexpected server errors. Forms preserve entered values after validation failures. Destructive product deletion and terminal order actions require confirmation.

## Testing strategy

Implementation follows test-driven development.

Laravel feature tests cover protected product detail, protected order detail, private customer fields, dashboard summaries, and role enforcement.

Nuxt tests cover session behavior without client token exposure, middleware redirects, request mapping, product variants, filters, allowed order actions, status formatting, placeholder routes, and print-data formatting.

End-to-end verification covers login, refresh persistence, logout, product creation/edit/deletion, order filtering and progression, printing, customer-role rejection, placeholder navigation, and the mobile drawer.

Final verification requires Laravel tests, frontend tests, the Nuxt production build, direct SSR route rendering, and browser checks at mobile and desktop sizes without console or hydration errors.

## Out of scope

- Customer management APIs and screens beyond the placeholder
- Delivery-zone CRUD beyond the placeholder
- Promotions, reports, and settings functionality
- Cloudinary uploads
- Password recovery
- Server-generated PDF invoices
- Dashboard charts without real time-series data
- API `/v1` migration
- Billplz payment implementation changes

## Acceptance criteria

- The visual hierarchy closely matches the supplied nine-screen reference.
- Core screens use real protected Laravel data and mutations.
- Customer accounts cannot access admin data or actions.
- The Sanctum token is inaccessible to browser JavaScript.
- Product and order pages work after direct refresh.
- Only valid order-status transitions are offered and accepted.
- Placeholder navigation is intentional and contains no dead screens.
- Invoice content prints cleanly through the native browser dialog.
- Desktop and mobile navigation match the reference behavior.
- Existing storefront behavior and unrelated working-tree changes remain intact.
