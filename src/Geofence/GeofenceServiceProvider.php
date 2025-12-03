<?php

namespace Jxci\Geofence;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class GeofenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = \dirname(__DIR__, 2) . '/config/geofence.php';

        $this->mergeConfigFrom($configPath, 'geofence');

        $this->app->singleton(DaData\DaDataService::class, function ($app) {
            $service = new DaData\DaDataService('dadata');
            $service->setLogger(Log::channel('geofence'));
            return $service;
        });

        $this->app->singleton(YandexGeo\YandexGeoService::class, function ($app) {
            $service = new YandexGeo\YandexGeoService('yandex_geo');
            $service->setLogger(Log::channel('geofence'));
            return $service;
        });

        $this->app->singleton(GeofenceManager::class, function ($app) {
            $config = $app['config']->get('geofence', []);

            return new GeofenceManager(
                $app->make(DaData\DaDataService::class),
                $app->make(YandexGeo\YandexGeoService::class),
                $config
            );
        });
    }

    public function boot(): void
    {
        $configPath = \dirname(__DIR__, 2) . '/config/geofence.php';

        $this->publishes([
            $configPath => config_path('geofence.php'),
        ], 'config');
    }
}


