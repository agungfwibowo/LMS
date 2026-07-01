---
name: elegant-glass-ui
description: "Use whenever building or editing frontend UI in this project — Blade views, Livewire/Flux components, Tailwind classes, landing/public pages, dashboards, cards, navbars, modals, badges. Enforces the SIPAHAM design language: calm & elegant first, MEASURED glassmorphism (accents only), consistent brand tokens, subtle animation with reduced-motion support, and reusable-component-first. Do NOT use for backend PHP logic, DB, or routing."
metadata:
  author: agung
  type: design-language
---

# Elegant Glass UI — SIPAHAM Design Language

The visual design language for this app. It sits ABOVE the technical skills
(`fluxui-development`, `tailwindcss-development`): those say *how* to write
Flux/Tailwind; this says *what the result must look and feel like*.

Context: SIPAHAM is an e-learning platform for a **government hospital**
(RSUP H. Adam Malik). The mood is **calm, clean, elegant, professional and
credible** — never flashy. Glass is a spice, not the main dish.

## The 5 Principles (in priority order)

1. **Reusable-component-first** — never hand-roll markup that a component covers.
2. **Consistency via tokens** — no magic hex/px; use the defined tokens.
3. **Calm & elegant** — restraint, whitespace, clear hierarchy.
4. **Measured glassmorphism** — one recipe, accents only.
5. **Subtle animation** — short, meaningful, reduced-motion aware.

---

## 1. Reusable-component-first (rule #0, non-negotiable)

Before writing any new markup, in this order:

1. **Flux Free component exists?** Use it. Available free: `avatar, badge, brand,
   breadcrumbs, button, callout, card, checkbox, dropdown, field, heading, icon,
   input, modal, navbar, otp-input, pagination, profile, progress, radio, select,
   separator, skeleton, switch, table, text, textarea, toast, tooltip`.
2. **Project Blade/Livewire component exists?** Check `resources/views/components/`
   (e.g. `landing/*`, `header`, `app-breadcrumbs`, `star-rating`) before creating.
3. **Markup repeats ≥2×?** Extract into a `<x-...>` or Flux component — do not copy-paste.

Styling goes through **tokens/utilities**, never inline hardcoded values.
Icons: Heroicons names only (verify on heroicons.com); Lucide via `php artisan flux:icon`.

## 2. Consistency via tokens — already wired in `global.css`

Do NOT invent new colors. Use the existing `@theme` tokens:

| Purpose | Token / utility |
|---|---|
| Brand teal scale | `bg-brand-600`, `text-brand-700`, `border-brand-200` … (`50`–`950`) |
| Interactive accent | `bg-accent`, `text-accent`, `ring-accent`, `text-accent-foreground` |
| Lime highlight (sparingly) | `text-lime` / `bg-lime` via `--color-lime` |
| Neutrals | `zinc-*` (remapped to slate) — `bg-zinc-50`, `text-zinc-600`, etc. |
| Heading font | `font-heading` (Plus Jakarta Sans) |
| Body font | `font-sans` (Public Sans) — default |

- Adaptive dark+light is mandatory: every surface needs `dark:` counterparts,
  matching the `.dark` variant already used across the project.
- Radius: stick to `rounded-lg` / `rounded-xl` / `rounded-full`. Avoid random values.
- New design tokens go in `global.css` `@theme` — never as one-off hex in a Blade file.

## 3. Calm & elegant

- **Whitespace is a feature.** Prefer generous `gap`/padding over dense layouts.
  Use `gap` utilities between siblings, not margins.
- **Hierarchy:** one clear focal point per view. `font-heading` + weight for titles,
  `text-zinc-500/600` for secondary text.
- **Restraint:** remove elements before adding. No decoration without function.
  Limited accent usage — teal for primary action, lime only as rare highlight.
- **Data-heavy areas stay flat** (tables, forms, article body `prose-berita`) —
  solid surfaces, no glass, no float.

## 4. Measured glassmorphism — ONE recipe

Glass is allowed ONLY on accent surfaces: **sticky navbars/topbars, floating
cards over imagery, overlay/modal chrome, small chips/badges**. It is BANNED on
text-heavy content, tables, and forms.

Use these three canonical recipes — replace the scattered ad-hoc
`bg-white/70`/`/90`/`backdrop-blur-2xl` variants with them:

```blade
{{-- Bar glass: navbar, sticky filter bar --}}
<nav class="sticky top-0 z-40 border-b border-zinc-200/70 bg-white/80
            backdrop-blur-md transition-[background,box-shadow] duration-200
            dark:border-white/10 dark:bg-zinc-950/70">…</nav>

{{-- Surface glass: floating card / stat card over color or image --}}
<div class="rounded-xl border border-white/60 bg-white/70 shadow-sm backdrop-blur-md
            dark:border-white/10 dark:bg-zinc-900/60">…</div>

{{-- Chip glass: badge over imagery --}}
<span class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-brand-700
             backdrop-blur dark:bg-white/10 dark:text-brand-300">…</span>
```

Rules:
- Blur ceiling is `backdrop-blur-md`. No `-2xl`/`-3xl` (too dreamy, hurts credibility).
- Keep opacity ≥ `/70` in light and ≥ `/60` in dark so text stays readable.
- Gate blur for graceful fallback: `supports-[backdrop-filter]:bg-white/70` with a
  more-opaque base (`bg-white/90`) when unsupported.
- Never put long body text directly on glass — put it on the solid layer beneath.

## 5. Subtle animation

Standard scale (reuse what exists in `animation.css`):

- **Durations:** hover/focus `duration-150`, state change `duration-200`,
  enter/leave `duration-300`. Nothing longer than 300ms for UI feedback.
- **Easing:** `ease-out` for enter; the existing reveal easing for scroll.
- **Reuse, don't reinvent:** scroll entrance = `[data-reveal]` (+ `from-left`/`from-right`),
  hero motion = `animate-float`, progress bars = `.progress`.
- **Animate only meaning:** hover, focus, open/close, state change. No looping
  decoration on content.

**Reduced-motion is REQUIRED (currently missing project-wide).** Guard every
non-essential animation:

- In Blade: use `motion-safe:` / `motion-reduce:` variants
  (e.g. `motion-safe:transition motion-safe:duration-200`).
- Better: add a global guard in `animation.css` so existing `[data-reveal]`,
  `.animate-float`, `.progress` respect it:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: .01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: .01ms !important;
    scroll-behavior: auto !important;
  }
}
```

---

## Definition of Done (check before finishing any UI work)

- [ ] Reused Flux/project component; extracted anything repeated ≥2×
- [ ] Only brand/accent/zinc tokens — no magic hex or px
- [ ] Light **and** dark variants present and legible
- [ ] Glass only on accent surfaces, one recipe, blur ≤ `md`, readable opacity
- [ ] Animation ≤300ms, meaningful, and respects reduced-motion
- [ ] Focus states visible (`ring-accent`), responsive at sm/md/lg
- [ ] After Blade/PHP edits: `vendor/bin/pint --dirty --format agent`; rebuild via
      `npm run dev`/`build` if UI change isn't visible

## Common pitfalls (seen in this codebase)

- Scattered glass values (`bg-white/70`, `/90`, `backdrop-blur-2xl`) → use the recipe.
- Animations with no `prefers-reduced-motion` guard.
- Hardcoded teal hex instead of `brand-*`/`accent` tokens.
- Glass behind long text (hurts readability + credibility).
- Building custom markup when a Flux Free component already exists.
