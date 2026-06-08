# Maritime E-Learning SCORM 

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

Install Breeze after dependencies are available:

```bash
php artisan breeze:install blade
php artisan migrate
```

## Architecture

- `app/Domain`: domain models and module-specific contracts.
- `app/Services`: application services, including SCORM package and tracking orchestration.
- `app/Repositories`: query and persistence boundaries for larger modules.
- `app/Http`: controllers, middleware, and form requests.
- `resources/views`: Blade screens for dashboards, courses, and SCORM player.
