<?php

namespace App\Support\Geofence\DaData;

use App\Support\Geofence\DaData\Results\DaDataErrorResult;
use App\Support\Geofence\DaData\Results\DaDataSuggestResult;
use Illuminate\Support\Facades\Facade;

/**
 * DaData Facade
 *
 * @method static DaDataSuggestResult|DaDataErrorResult geocode(string $address, array $options = [])
 * @method static DaDataSuggestResult|DaDataErrorResult reverseGeocode(float $latitude, float $longitude, array $options = [])
 * @method static DaDataSuggestResult|DaDataErrorResult suggest(string $query, int $count = 10, array $options = [])
 * @method static DaDataSuggestResult|DaDataErrorResult validate(string $address, array $options = [])
 * @method static DaDataSuggestResult|DaDataErrorResult geolocateList(float $latitude, float $longitude, string $streetKladrId, string $house)
 * @method static bool isEnabled()
 * @method static array getConfig()
 */
class DaData extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'geofence.dadata';
    }
}
