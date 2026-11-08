# Local Machine Command Reference: Dev ⇋ Production Switch

Quick CLI command recipes to toggle your local Laravel project between active **Local Development Mode** and **Production Build / Packaging Mode**.

---

## 1. Switch: Local Dev ➔ Production Ready (Before Packaging)

Run these commands when you are done coding and want to strip dev tools, compile assets, and build a clean `vendor` folder for the zip archive.

```bash
# 1. Build frontend assets for production (compresses & removes source maps)
npm run build

# 2. Prune dev packages (removes debugbar, faker, phpunit, collision, etc.)
#    and generate an optimized classmap autoloader
composer install --optimize-autoloader --no-dev

# 3. CRITICAL: Clear all caches so local env values & paths are NOT baked into the zip
php artisan optimize:clear

# 4. Remove local logs and session cache files
rm -rf storage/logs/*.log
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
```

> **Why `optimize:clear` is mandatory here:** If you run `config:cache` locally, Laravel bundles your local database credentials, `APP_URL=http://localhost`, and local filesystem paths into `bootstrap/cache/config.php`. Packaging that file will break the site on cPanel.

---

## 2. Switch: Production Ready ➔ Local Dev (Resume Coding)

Run these commands after you finish exporting your zip archive and want to get back to writing code locally with full debugging tools.

```bash
# 1. Reinstall all dev dependencies (PHPUnit, Faker, Debugbar, Telescope, Pint, etc.)
composer install

# 2. Reinstall npm packages if any were touched
npm install

# 3. Clear any cached state
php artisan optimize:clear

# 4. (Optional) Run local dev servers
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev
```

---

## 3. Handy Single-Line Shortcuts (Aliases)

Add these to your `~/.bashrc` or `~/.zshrc` (or run them as inline commands):

### Turn to Production:
```bash
npm run build && composer install --optimize-autoloader --no-dev && php artisan optimize:clear
```

### Turn back to Local Dev:
```bash
composer install && php artisan optimize:clear
```

---

## Summary Comparison

| Task | Local Dev Mode | Production Package Mode |
|---|---|---|
| **Vite / Frontend** | `npm run dev` (hot reload) | `npm run build` (minified bundle) |
| **Composer Deps** | `composer install` (includes `require-dev`) | `composer install --no-dev --optimize-autoloader` |
| **Debug Tools** | Enabled (`APP_DEBUG=true`) | Excluded from `vendor/` |
| **Laravel Cache** | `optimize:clear` (active reading) | `optimize:clear` (un-cached before packaging) |