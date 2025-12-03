<?php

namespace App\Support\Geofence\YandexGeo;

use App\Support\Geofence\YandexGeo\Enums\YandexGeoLanguage;
use App\Support\Geofence\YandexGeo\Results\YandexGeoErrorResult;
use App\Support\Geofence\YandexGeo\Results\YandexGeoGeocodeResult;
use Illuminate\Support\Facades\Facade;

/**
 * YandexGeo Facade
 *
 * @method static YandexGeoGeocodeResult|YandexGeoErrorResult geocode(string $address, array $options = [])
 * @method static YandexGeoGeocodeResult|YandexGeoErrorResult reverseGeocode(float $latitude, float $longitude, array $options = [])
 * @method static YandexGeoGeocodeResult|YandexGeoErrorResult suggest(string $query, int $count = 10, array $options = [])
 * @method static YandexGeoGeocodeResult|YandexGeoErrorResult validate(string $address, array $options = [])
 * @method static YandexGeoGeocodeResult|YandexGeoErrorResult searchByCoordinates(float $latitude, float $longitude, array $options = [])
 * @method static bool isFallbackCoordinates(float $latitude, float $longitude)
 * @method static YandexGeoLanguage getLanguage()
 * @method static array getSupportedLanguages()
 * @method static bool isLanguageSupported(string $language)
 * @method static bool isEnabled()
 * @method static array getConfig()
 */
class YandexGeo extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'geofence.yandex_geo';
    }
}
