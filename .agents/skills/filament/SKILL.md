---
name: Filament Resource Management
description: Guidelines for building premium Filament admin interfaces with consistent design.
---

# Filament Management Skill

This skill provides directions for developing the administration panel for `litterfreeleeds`.

> [!IMPORTANT]
> All UI components must reflect the project's premium design philosophy: clean layouts, subtle interactive elements, and meaningful feedback.

## Core Responsibilities

1. **Resource Generation**: Use standard Filament commands to create consistent modules.
2. **Schema Definition**: Define clear, accessible forms and tables with proper validation and tooltips.
3. **Design Excellence**: Ensure all new pages meet the project's high standard for clarity and responsiveness.

## Standard Commands

### Creating a New Resource
Always create resources within the standard `app/Filament/Resources` directory:
```bash
php artisan make:filament-resource <Name>
```

### Adding a Widget
For dashboards or resource pages:
```bash
php artisan make:filament-widget <Name>
```

## Design Principles

- **Field Descriptions**: Provide help text for complex form fields.
- **Table Filters**: Add sensible default filters to tables for ease of use.
- **Flash Messaging**: Use Filament's built-in notification system for success/error feedback.
- **Grids**: Use responsive column layouts (e.g., `Grid::make()->columns(2)`).

## Accessibility
Ensure all interactive elements have labels and that the navigation remains intuitive as the application grows.
