<?php

namespace App\Support\Geofence\DaData\Results;

use App\Support\Geofence\Enums\GeofenceErrorType;
use App\Support\Geofence\Results\ErrorResult;

/**
 * Результат с ошибкой DaData
 */
class DaDataErrorResult extends ErrorResult
{
    public function getRecommendations(): array
    {
        return match($this->errorType) {
            GeofenceErrorType::DADATA_API_ERROR => [
                'Проверьте правильность API ключа и секрета DaData.',
                'Убедитесь, что ваш аккаунт DaData активен и не заблокирован.',
                'Проверьте корректность запроса к API DaData.',
            ],
            GeofenceErrorType::DADATA_NETWORK_ERROR => [
                'Проверьте сетевое соединение сервера.',
                'Попробуйте увеличить таймаут запроса в конфигурации.',
                'Проверьте доступность API DaData.',
            ],
            GeofenceErrorType::DADATA_QUOTA_ERROR => [
                'Превышена квота запросов к DaData API.',
                'Проверьте лимиты вашего тарифного плана.',
                'Рассмотрите возможность обновления тарифа.',
            ],
            GeofenceErrorType::DADATA_VALIDATION_ERROR => [
                'Проверьте корректность входных данных.',
                'Убедитесь, что адрес указан правильно.',
                'Попробуйте упростить запрос.',
            ],
            default => [
                'Произошла неизвестная ошибка DaData.',
                'Проверьте логи для получения дополнительной информации.',
            ],
        };
    }
}
