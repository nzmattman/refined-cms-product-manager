<?php

namespace RefinedDigital\ProductManager\Module\Providers;

use Illuminate\Support\ServiceProvider;
use RefinedDigital\CMS\Modules\Core\Models\PublicRouteAggregate;
use RefinedDigital\ProductManager\Commands\Install;
use RefinedDigital\CMS\Modules\Core\Models\PackageAggregate;
use RefinedDigital\CMS\Modules\Core\Models\ModuleAggregate;
use RefinedDigital\CMS\Modules\Core\Models\RouteAggregate;

class ProductManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->addNamespace('productManager', [
            base_path().'/resources/views',
            __DIR__.'/../Resources/views',
        ]);

        if ($this->app->runningInConsole()) {
            if (\DB::connection()->getDatabaseName() && !\Schema::hasTable('products')) {
                $this->commands([
                    Install::class,
                ]);
            }
        }

        $this->publishes([
            __DIR__.'/../../../config/product-manager.php' => config_path('product-manager.php'),
        ], 'product-manager');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        app(RouteAggregate::class)
            ->addRouteFile('productManager', __DIR__.'/../Http/routes.php');
        app(PublicRouteAggregate::class)
            ->addRouteFile('productManager', __DIR__.'/../Http/public-routes.php');


        $this->mergeConfigFrom(__DIR__.'/../../../config/product-manager.php', 'ProductManager');

        $children = [
            (object) [ 'name' => 'Products', 'route' => 'products', 'activeFor' => ['products']],
            (object) [ 'name' => 'Variation Types', 'route' => 'product-variations', 'activeFor' => ['product-variations']],
            // (object) [ 'name' => 'Delivery Options', 'route' => 'delivery-options', 'activeFor' => ['delivery-options']],
        ];

        if (config('product-manager.orders.active')) {
            $children[] = (object) [ 'name' => 'Orders', 'route' => 'orders', 'activeFor' => ['orders']];
        }

        $menuConfig = [
            'order' => 600,
            'name' => 'Product Manager',
            'icon' => 'fas fa-gift',
            'route' => 'products',
            'activeFor' => ['products','product-variations', 'delivery-options'],
            'children' => $children
        ];

        app(ModuleAggregate::class)
            ->addMenuItem($menuConfig);

        app(PackageAggregate::class)
            ->addPackage('ProductManager', [
                'repository' => \RefinedDigital\ProductManager\Module\Http\Repositories\ProductRepository::class,
                'model' => '\\RefinedDigital\\ProductManager\\Module\\Models\\Product',
            ]);
    }
}
