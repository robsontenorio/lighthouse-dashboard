<p align="center">
  <img src="dashboard.png">  
</p>
<p align="center">
    <img src="https://img.shields.io/packagist/v/robsontenorio/lighthouse-dashboard.svg" />
    <img src="https://img.shields.io/packagist/dt/robsontenorio/lighthouse-dashboard.svg" />
</p>

# Analytics dashboard for Laravel Lighthouse GraphQL

**WARNING: WORK IN PROGRESS!**

This package adds a standalone analytics dasbhoard with metrics collected from  [Laravel Lighthouse GraphQL Server](https://lighthouse-php.com/).

<kbd>
    <img src="readme.png">
</kbd>

# Install 

Enable the `Tracing` extension, by adding the service provider to your `config/app.php`, as described on oficial Laravel Lighthouse documentation. Note this is not a feature from this package.

```php
'providers' => [
    \Nuwave\Lighthouse\Tracing\TracingServiceProvider::class,
],
```

Require the package.

```
composer require robsontenorio/lighthouse-dashboard
```

Run install command.

```
php artisan lighthouse-dashboard:install
```

Open the dashboard.

```
http://your-app/lighthouse-dashboard
```

Optional, but important.

To keep the assets up-to-date and avoid issues in future updates, we highly recommend adding the command to the post-autoload-dump section in your `composer.json` file:

```json
{
    ...
    "scripts": {
        "post-autoload-dump": [
            ...
            "@php artisan lighthouse-dashboard:install --ansi"
        ]
    }
}
```

# How does it works?

By enabling `Tracing` extension on Laravel Lighthouse GraphQL Server, every operation automatically is profiled with its execution metrics.

- GraphQL request is made.
- Dashboard listen to `ManipulateResult` event and collect metrics from current operation.
- Metrics are stored on dashboard.


# Local development

Once this package includes UI, the only way to test it is by running it through target app.

While developing locally this approach allows you to get UI instant refresh if you need to modify it.

## Uninstall  

If you previous installed this package, first uninstall it **from target app**.

Remove this entry from `composer.json`.

```json
{
    ...
    "scripts": {
        "post-autoload-dump": [
            ...
            "@php artisan lighthouse-dashboard:install --ansi"
        ]
    }
}
```

Remove installed package assets folder.

```
rm -rf /path/to/app/public/vendor/lighthouse-dashboard
```

Remove package.

```
composer remove robsontenorio/lighthouse-dashboard
```

Then package public assets from target app.

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

Require local package version.

```sh
composer require robsontenorio/lighthouse-dashboard @dev
```

Create symlink from target app `/public` assets to this package assets.

```sh
ln -s /path/to/app/vendor/robsontenorio/lighthouse-dashboard/public/vendor /path/to/app/public/vendor
```

From target app enter to package vendor folder.

```sh
cd vendor/robsontenorio/lighthouse-dashboard
```

Install composer dependencies.

```sh
composer install
```

Install frontend dependencies and start it on dev mode.

```sh
yarn dev
```

Then point to http://127.0.0.1:3000/lighthouse-dashboard/

## Tests