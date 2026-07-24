# How to Order Page Recreation

## Goal

Recreate the supplied “How to order” reference as a responsive Nuxt page for Lyn’s Little Kitchen. The result should closely match the reference while remaining accessible, functional, and consistent with the existing storefront.

## Scope

- Replace the presentation of `frontend/app/pages/how-to-order.vue`.
- Continue using the shared storefront header and footer.
- Preserve existing routing, cart state, and storefront conventions.
- Add only page-specific assets and styles required by the recreation.
- Do not modify backend behavior or unrelated storefront pages.

## Visual Direction

The page uses a warm bakery-editorial direction: soft ivory surfaces, cocoa-brown serif headings, terracotta actions, fine peach borders, and restrained botanical line art. The supplied composition is the source of truth for spacing, hierarchy, copy, imagery, and overall balance.

The desktop layout contains:

1. A split hero with editorial copy on the left and cookie-gift photography on the right.
2. A five-step alternating journey with numbered cards, supporting photographs, and curved terracotta connectors.
3. A wide delivery banner with a bakery van illustration, delivery message, and shop call to action.

On smaller screens, the hero becomes a single column and the journey becomes a clear vertical sequence. Connector artwork may be simplified or hidden where it would interfere with legibility.

## Assets

Crop the photographic regions from the supplied PNG into local page assets. Rebuild text, buttons, cards, numbering, borders, and layout as live HTML/CSS rather than embedding the full screenshot. Decorative botanical and connector elements should be recreated with lightweight inline SVG or CSS.

## Interaction and Accessibility

- “Start your order” and “Choose your cookies” navigate to `/shop`.
- Images receive useful alternative text, with purely decorative graphics hidden from assistive technology.
- Interactive elements retain visible keyboard focus.
- Text contrast and touch targets remain usable on mobile.
- Reduced-motion preferences are respected if entrance effects are included.

## Implementation Shape

Keep step content in a typed data array and render it with Vue. Use scoped page CSS for the composition so shared storefront styles are not disturbed. Reuse existing global button and layout conventions where they visually fit; page-specific variants may be introduced locally when needed for fidelity.

## Verification

- Run the existing storefront tests and production build.
- Add a focused structural test for the page’s five steps and shop links if the current test approach supports it.
- Render the desktop and mobile page in a browser and compare against the supplied reference.
- Check that the shared navigation, cart link, calls to action, and responsive stacking still work.

