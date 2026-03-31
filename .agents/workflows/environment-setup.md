---
description: Steps to set up the development environment for Litter Free Leeds.
---

# Environment Setup Workflow

Follow these steps to quickly prepare your environment for a new session or development task.

## Prerequisites
- Docker & Laravel Sail
- Composer

## Steps

### 1. Initialize Environment
Ensure the environment file exists:
```bash
cp .env.example .env
```
Generate the application key:
```bash
php artisan key:generate
```

### 2. Start Services
Launch the Docker containers:
// turbo
```bash
./vendor/bin/sail up -d
```

### 3. Database Migration
Align the database schema:
// turbo
```bash
./vendor/bin/sail artisan migrate
```

### 4. Build Assets
Compile the frontend assets:
// turbo
```bash
./vendor/bin/sail npm run dev
```

### 5. Access Application
The application should now be accessible at `http://localhost`.
The Filament admin panel is located at `http://localhost/admin`.
