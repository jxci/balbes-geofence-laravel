<?php

namespace App\Support\Geofence\Enums;

use App\Support\Geofence\DaData\DaData;
use App\Support\Geofence\DaData\DaDataService;
use App\Support\Geofence\YandexGeo\YandexGeo;
use App\Support\Geofence\YandexGeo\YandexGeoService;

/**
 * Типы геосервисов
 */
enum GeofenceServiceType: string
{
    case DADATA = 'dadata';
    case YANDEX_GEO = 'yandex_geo';

    /**
     * Получить человекочитаемое название
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::DADATA => 'DaData',
            self::YANDEX_GEO => 'Yandex Geo',
        };
    }

    /**
     * Получить класс сервиса
     */
    public function getServiceClass(): string
    {
        return match ($this) {
            self::DADATA => DaDataService::class,
            self::YANDEX_GEO => YandexGeoService::class,
        };
    }

    /**
     * Получить класс фасада
     */
    public function getFacadeClass(): string
    {
        return match ($this) {
            self::DADATA => DaData::class,
            self::YANDEX_GEO => YandexGeo::class,
        };
    }
}
