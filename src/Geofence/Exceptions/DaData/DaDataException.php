<?php

namespace App\Support\Geofence\Exceptions\DaData;

use App\Support\Geofence\Exceptions\GeofenceException;

/**
 * Базовое исключение для DaData сервиса
 */
abstract class DaDataException extends GeofenceException
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
            'DaData',
            $method,
            $context,
            $code,
            $previous
        );
    }

    public function getErrorType(): string
    {
        return 'DaData';
    }

    public function getRecommendations(): array
    {
        return [
            'Проверьте корректность API токена и секретного ключа',
            'Убедитесь, что у вас есть доступ к DaData API',
            'Проверьте лимиты запросов к API',
            'Попробуйте повторить запрос через некоторое время',
        ];
    }
}
