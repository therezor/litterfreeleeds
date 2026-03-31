---
description: How to add a new feature to the Litter Free Leeds project.
---

# Feature Development Workflow

Follow these steps to implement a new module or functionality using Laravel and Filament.

## Phases

### 1. Database & Models
Start with the data layer. 
1. Create a migration: `php artisan make:migration create_<table_name>_table`
2. Define the schema in the migration file.
3. Create the model: `php artisan make:model <ModelName>`
4. Add fillable properties and relationships.

### 2. Filament Resources
Generate the admin interface.
1. Run the resource generator: `php artisan make:filament-resource <ModelName>`
2. Configure the `form` method in the generated resource file.
3. Configure the `table` method with search, filters, and bulk actions.
4. Customize the navigation label and icon.

### 3. Business Logic
Implementation of specific rules.
1. Create actions or service classes for complex logic.
2. Integrate these into Filament resource pages or buttons.

### 4. Verification
Ensure everything works as expected.
1. Run local tests: `php artisan test`
2. Manually verify the UI in the development environment.
