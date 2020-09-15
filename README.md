# Analytics dashboard for Laravel Lighthouse GraphQL Server

**WARNING: WORK IN PROGRESS!**

This package adds a standalone analytics dasbhoard with metrics collected from  [Laravel Lighthouse GraphQL Server](https://lighthouse-php.com/).

<img src="readme.png">

<br><br>

# Install 

On target app enable the `Tracing` feature, by adding the service provider to your `config/app.php`, as described on oficial Laravel Lighthouse documentation.  

```php
'providers' => [
    \Nuwave\Lighthouse\Tracing\TracingServiceProvider::class,
],
```

Require the package.

```
composer require robsontenorio/lighthouse-dashboard
```
Run migrations and publish assets

> TODO: replace this two steps with `php artisan lighthouse-dashboard:install` 

```
php artisan migrate
```

```
php artisan vendor:publish
```

Now point to http://your-app/lighthouse-dashboard

# Local development

Once this package includes UI, the only way to test it is by running it through target app.

While developing locally this approach allows you to get UI instant refresh if you need to modify it.

## Uninstall  

If you previous installed this package, first uninstall it from target app.

```
composer remove robsontenorio/lighthouse-dashboard
```

Then remove its public assets from target app.

```
rm -rf /path/to/app/public/vendor/lighthouse-dashboard
```

## Install locally

On target app add to `composer.json`

```json
 "repositories": {
        "robsontenorio/lighthouse-dashboard": {
            "type": "path",
            "url": "/local/path/to/package/lighthouse-dashboard"
        }
    }
```

Require the package again.

```sh
composer require robsontenorio/lighthouse-dashboard
```

Create symlink from target app `/public` assets to this package assets.

```sh
cd /path/to/app/public

ln -s ../vendor/robsontenorio/lighthouse-dashboard/public/vendor/ vendor
```

From target app enter to package vendor folder.

```sh
cd /path/to/app/vendor/robsontenorio/lighthouse-dashboard
```

Install composer dependencies.

```sh
composer install
```

Install frontend dependencies and start it on dev mode.

```sh
yarn install && yarn dev
```

Then point to http://127.0.0.1:3000/lighthouse-dashboard/

## Tests