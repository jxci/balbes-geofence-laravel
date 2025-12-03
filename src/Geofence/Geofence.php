<?php

namespace Jxci\Geofence;

use Jxci\Geofence\Enums\GeofenceServiceType;
use Jxci\Geofence\Results\BaseResult;
use Illuminate\Support\Facades\Facade;

/**
 * Geofence Facade с fluent API
 *
 * @method static GeofenceManager use(GeofenceServiceType $service)
 * @method static GeofenceManager daData()
 * @method static GeofenceManager yandexGeo()
 * @method static BaseResult geocode(string $address, array $options = [])
 * @method static BaseResult reverseGeocode(float $latitude, float $longitude, array $options = [])
 * @method static BaseResult suggest(string $query, int $count = 10, array $options = [])
 * @method static BaseResult validate(string $address, array $options = [])
 *
 * Возможные типы возврата:
 * - DaDataSuggestResult|DaDataErrorResult (при использовании DaData)
 * - YandexGeoGeocodeResult|YandexGeoErrorResult (при использовании YandexGeo)
 * - BaseResult (общий тип для fallback логики)
 */
class Geofence extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     */
    protected static function getFacadeAccessor(): string
    {
        return GeofenceManager::class;
    }
}
