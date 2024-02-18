# Installation Instructions

## Prerequisites

 * [PHP v8.1 or higher](http://php.net)
 * [Composer](https://getcomposer.org/)
 * [NodeJS](https://nodejs.org/en)
 * [Laravel 10.x server requirements](https://laravel.com/docs/10.x/deployment#server-requirements)
 * Additional PHP extensions:
   * [GNU Multiple Precision (GMP)](https://www.php.net/manual/en/gmp.setup.php)
   * [Image Processing and Generation (GD)](https://www.php.net/manual/en/image.setup.php)

## Steps

1. Navigate to the root directory of your Laravel project:
   ```bash
   cd /path/to/sameoldwebsite
   ```

2. Install all required PHP dependencies listed in the ``composer.json`` file:
   ```bash
   composer install
   ```
3. Duplicate the ``.env.example`` file and rename it to ``.env``:
   ```bash
   cp .env.example .env
   ```

4. Generate a unique application key:
   ```bash
   php artisan key:generate
   ```
   
5. Generate secrets for JSON Web Tokens (JWTs):
```bash
# Generates secret for authentication tokens:
sed -i "s/^LITTLEJWT_KEY_PHRASE=.*/LITTLEJWT_KEY_PHRASE=$(php artisan littlejwt:phrase -d | sed -n '/.*/{n;p}')/" .env
# Generates secret for refresh tokens:
sed -i "s/^LITTLEJWT_KEY_PHRASE_REFRESH=.*/LITTLEJWT_KEY_PHRASE_REFRESH=$(php artisan littlejwt:phrase -d | sed -n '/.*/{n;p}')/" .env
```

6. Update the database connection settings and any other configuration variables in the .env file according to your environment.
   * Ensure the ``APP_URL`` variable is set to the app's URL. If set incorrectly, the admin panel won't work properly.
   * Services like e-mail, CAPTCHA, OAuth, etc. won't function unless the configuration variables are set correctly.

7. Run migrations to set up the database schema:
   ```bash
   php artisan migrate:fresh
   ```
   
8. Populate the database by seeding it:

   ```bash
   # Required:
   php artisan db:seed SetupSeeder
   # Optional:
   php artisan db:seed InitialSeeder
   ```
   
9. Create a symbolic link to the storage directory:
   ```bash
   php artisan storage:link
   ```
10. Install all required NodeJS packages:
   ```bash
   # Using NPM:
   npm install
   # Using Yarn:
   yarn install
   ```
11. Build the front-end assets for production:
   ```bash
   # Using NPM:
   npm run build
   # Using Yarn:
   yarn run build
   ```

12.  Serve the web app:

```bash
php artisan serve
```

13. Open the web app in your web browser.

## Additional

If the initial seeder was used, the default username / password is `admin@sameoldnick.com` / `secret`.

You can optimize the web app by caching files (production only):
```bash
php artisan optimize
```

