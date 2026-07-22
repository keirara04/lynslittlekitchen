# Vue Router Devtools Null Guard

## Problem

In development, `vue-router@5.2.0` attaches devtools metadata to each internal
`RouterView` component reference. Nuxt can supply a reference entry whose
component instance is `null`. Vue Router writes to that value without checking
it first, causing the client-side 500 error:

```text
null is not an object (evaluating 'instance.__vrv_devtools = info')
```

The application page itself renders successfully on the server and production
builds complete successfully.

## Design

Add a small, project-owned Vite transform to the Nuxt configuration. The
transform will apply only to Vue Router's source, find the exact devtools
metadata assignment, and add a null check before the assignment.

The transform will fail loudly during the build if the expected upstream source
is no longer present. This prevents a future Vue Router update from silently
leaving an obsolete or ineffective workaround in place.

The workaround will not modify `node_modules`, page routing, or production
application behavior. It can be removed once Vue Router guards the internal
instance itself.

## Verification

- A focused Node test will run the transform against representative Vue Router
  source and verify that a null instance is skipped.
- The test will verify that unrelated modules are unchanged.
- `npm run build` will confirm that Nuxt still compiles successfully.
- The development server will be restarted and `/` will be requested to confirm
  that server rendering still returns HTTP 200 with the expected page content.

