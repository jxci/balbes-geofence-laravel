<?php

namespace App\Support\Geofence\YandexGeo\Results;

use App\Support\Geofence\Enums\GeofenceErrorType;
use App\Support\Geofence\Results\ErrorResult;

/**
 * Результат ошибки YandexGeo
 */
class YandexGeoErrorResult extends ErrorResult
{
    public function getRecommendations(): array
    {
        return match($this->errorType) {
            GeofenceErrorType::YANDEX_GEO_API_ERROR => [
                'Проверьте правильность API ключа Yandex Geo.',
                'Убедитесь, что ваш аккаунт Yandex активен.',
                'Проверьте корректность запроса к API Yandex Geo.',
            ],
            GeofenceErrorType::YANDEX_GEO_NETWORK_ERROR => [
                'Проверьте сетевое соединение сервера.',
                'Попробуйте увеличить таймаут запроса в конфигурации.',
                'Проверьте доступность API Yandex Geo.',
            ],
            GeofenceErrorType::YANDEX_GEO_QUOTA_ERROR => [
                'Превышена квота запросов к Yandex Geo API.',
                'Проверьте лимиты вашего тарифного плана.',
                'Рассмотрите возможность обновления тарифа.',
            ],
            GeofenceErrorType::YANDEX_GEO_VALIDATION_ERROR => [
                'Проверьте корректность входных данных.',
                'Убедитесь, что координаты указаны правильно.',
                'Попробуйте упростить запрос.',
            ],
            default => [
                'Произошла неизвестная ошибка Yandex Geo.',
                'Проверьте логи для получения дополнительной информации.',
            ],
        };
    }
}