# Frontend Development Guidelines

Guidelines for building modern, bundled frontend experiences in this Laravel application using Tailwind CSS 4 and Alpine.js.

## Dependencies

- **Tailwind CSS v4**: Managed primarily through `@theme` blocks and `@custom-variant` in `resources/css/app.css`. Avoid `tailwind.config.js` unless specific legacy features are needed.
- **Alpine.js**: Must be bundled via NPM and imported into `resources/js/app.js`. Do not use CDN links in Blade templates.

## Conventions

### JavaScript (Alpine.js)

- **Bundling**: Import and initialize Alpine in `resources/js/app.js`.
  ```javascript
  import Alpine from 'alpinejs'
  window.Alpine = Alpine
  Alpine.start()
  ```
- **FOUC Prevention**: Keep theme initialization (Dark Mode) at the very top of `app.js` to ensure the correct class is applied as early as possible.
- **Persistent State**: Use `localStorage` inside Alpine components or initialization scripts for theme persistence.
- **Blade Integration**: Use `x-data` on stable parent elements (like `<body>`) to provide page-wide state management.

### CSS (Tailwind 4)

- **Class-based Dark Mode**: Always include `@custom-variant dark (&:where(.dark, .dark *));` in `app.css` to support class-based toggling.
- **`x-cloak`**: Always include `[x-cloak] { display: none !important; }` in the global CSS file to prevent Flicker of Uninitialized Content.
- **Customization**: Use the `@theme` block for variables (fonts, colors).

## Best Practices

- **Zero Inline Script**: Move all logic that exceeds a simple toggle into the bundled `app.js`.
- **Zero Inline Style**: Use Tailwind utilities or add classes to `app.css`.
- **Vite Integration**: Always use the `@vite` directive in the `<head>` of Blade templates.
