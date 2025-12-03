<?php

namespace App\Support\Geofence\Enums;

use App\Support\Geofence\Exceptions\DaData\DaDataApiException;
use App\Support\Geofence\Exceptions\DaData\DaDataNetworkException;
use App\Support\Geofence\Exceptions\DaData\DaDataQuotaException;
use App\Support\Geofence\Exceptions\DaData\DaDataValidationException;
use App\Support\Geofence\Exceptions\GeofenceConfigurationException;
use App\Support\Geofence\Exceptions\GeofenceFallbackException;
use App\Support\Geofence\Exceptions\GeofenceTimeoutException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoApiException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoNetworkException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoQuotaException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoValidationException;

/**
 * Типы ошибок геосервисов
 */
enum GeofenceErrorType: string
{
    # Общие ошибки
    case CONFIGURATION_ERROR = 'configuration_error';
    case TIMEOUT_ERROR = 'timeout_error';
    case FALLBACK_ERROR = 'fallback_error';
    case GEOFENCE_SERVICE_ERROR = 'geofence_service_error';

    # DaData ошибки
    case DADATA_API_ERROR = 'dadata_api_error';
    case DADATA_NETWORK_ERROR = 'dadata_network_error';
    case DADATA_QUOTA_ERROR = 'dadata_quota_error';
    case DADATA_VALIDATION_ERROR = 'dadata_validation_error';

    # Yandex Geo ошибки
    case YANDEX_GEO_API_ERROR = 'yandex_geo_api_error';
    case YANDEX_GEO_NETWORK_ERROR = 'yandex_geo_network_error';
    case YANDEX_GEO_QUOTA_ERROR = 'yandex_geo_quota_error';
    case YANDEX_GEO_VALIDATION_ERROR = 'yandex_geo_validation_error';

    /**
     * Получить человекочитаемое название
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::CONFIGURATION_ERROR => 'Configuration Error',
            self::TIMEOUT_ERROR => 'Timeout Error',
            self::FALLBACK_ERROR => 'Fallback Error',
            self::GEOFENCE_SERVICE_ERROR => 'Geofence Service Error',
            self::DADATA_API_ERROR => 'DaData API Error',
            self::DADATA_NETWORK_ERROR => 'DaData Network Error',
            self::DADATA_QUOTA_ERROR => 'DaData Quota Exceeded',
            self::DADATA_VALIDATION_ERROR => 'DaData Validation Error',
            self::YANDEX_GEO_API_ERROR => 'Yandex Geo API Error',
            self::YANDEX_GEO_NETWORK_ERROR => 'Yandex Geo Network Error',
            self::YANDEX_GEO_QUOTA_ERROR => 'Yandex Geo Quota Exceeded',
            self::YANDEX_GEO_VALIDATION_ERROR => 'Yandex Geo Validation Error',
        };
    }

    /**
     * Получить класс исключения
     */
    public function getExceptionClass(): string
    {
        return match ($this) {
            self::CONFIGURATION_ERROR => GeofenceConfigurationException::class,
            self::TIMEOUT_ERROR => GeofenceTimeoutException::class,
            self::FALLBACK_ERROR => GeofenceFallbackException::class,
            self::DADATA_API_ERROR => DaDataApiException::class,
            self::DADATA_NETWORK_ERROR => DaDataNetworkException::class,
            self::DADATA_QUOTA_ERROR => DaDataQuotaException::class,
            self::DADATA_VALIDATION_ERROR => DaDataValidationException::class,
            self::YANDEX_GEO_API_ERROR => YandexGeoApiException::class,
            self::YANDEX_GEO_NETWORK_ERROR => YandexGeoNetworkException::class,
            self::YANDEX_GEO_QUOTA_ERROR => YandexGeoQuotaException::class,
            self::YANDEX_GEO_VALIDATION_ERROR => YandexGeoValidationException::class,
        };
    }

    /**
     * Получить тип сервиса для ошибки
     */
    public function getServiceType(): ?GeofenceServiceType
    {
        return match ($this) {
            self::DADATA_API_ERROR,
            self::DADATA_NETWORK_ERROR,
            self::DADATA_QUOTA_ERROR,
            self::DADATA_VALIDATION_ERROR => GeofenceServiceType::DADATA,
            self::YANDEX_GEO_API_ERROR,
            self::YANDEX_GEO_NETWORK_ERROR,
            self::YANDEX_GEO_QUOTA_ERROR,
            self::YANDEX_GEO_VALIDATION_ERROR => GeofenceServiceType::YANDEX_GEO,
            default => null,
        };
    }
}
