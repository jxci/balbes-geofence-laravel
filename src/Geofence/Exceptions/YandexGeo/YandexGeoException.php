<?php

namespace App\Support\Geofence\Exceptions\YandexGeo;

use App\Support\Geofence\Exceptions\GeofenceException;

/**
 * Базовое исключение для Yandex Geo сервиса
 */
abstract class YandexGeoException extends GeofenceException
{
    public function __construct(
        string $message,
        string $method,
        array $context = [],
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct(
            $message,
            'YandexGeo',
            $method,
            $context,
            $code,
            $previous
        );
    }

    public function getErrorType(): string
    {
        return 'YandexGeo';
    }

    public function getRecommendations(): array
    {
        return [
            'Проверьте корректность API ключа Yandex',
            'Убедитесь, что у вас есть доступ к Yandex Geo API',
            'Проверьте лимиты запросов к API',
            'Попробуйте повторить запрос через некоторое время',
        ];
    }
}
