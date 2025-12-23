<?php

namespace App\Support\Geofence\DaData;

use App\Support\Geofence\AbstractGeoService;
use App\Support\Geofence\DaData\Results\DaDataErrorResult;
use App\Support\Geofence\DaData\Results\DaDataGeocodeResult;
use App\Support\Geofence\DaData\Results\DaDataSuggestResult;
use App\Support\Geofence\Enums\GeofenceErrorType;
use App\Support\Geofence\Results\BaseResult;
use App\Support\Geofence\Exceptions\DaData\DaDataApiException;
use App\Support\Geofence\Exceptions\DaData\DaDataNetworkException;
use App\Support\Geofence\Exceptions\DaData\DaDataQuotaException;
use App\Support\Geofence\Exceptions\DaData\DaDataValidationException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * DaData API сервис
 */
class DaDataService extends AbstractGeoService
{
    public function __construct()
    {
        parent::__construct('DaData');
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $this->getConfigValue('token'),
            'X-Secret' => $this->getConfigValue('secret'),
        ];
    }

    /**
     * Получить API URL
     */
    private function getApiUrl(): string
    {
        return $this->getConfigValue('base_uri', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/');
    }

    /**
     * Геокодирование адреса (поиск адресов)
     */
    public function geocode(string $address, array $options = []): DaDataSuggestResult|DaDataErrorResult
    {
        try {
            $requestData = [
                'query' => $address,
                'count' => $options['count'] ?? 10,
                'restrict_value' => $options['restrict_value'] ?? true,
                'locations' => $this->buildLocations($options),
            ];
            
            $this->logRequest('geocode', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest('suggest/address', $requestData),
                'geocode',
                ['address' => $address, 'options' => $options]
            );

            return new DaDataSuggestResult(
                $result,
                'DaData',
                'geocode',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new DaDataErrorResult(
                [],
                'DaData',
                'geocode',
                $e->getMessage(),
                GeofenceErrorType::DADATA_API_ERROR,
                $e->getCode(),
                ['address' => $address, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Обратное геокодирование (поиск адресов по координатам)
     * 
     * @param float $latitude Географическая широта
     * @param float $longitude Географическая долгота
     * @param array $options Опции запроса:
     *   - count (int): Количество результатов (максимум 20, по умолчанию 10)
     *   - radius_meters (int): Радиус поиска в метрах (максимум 1000, по умолчанию 100)
     *   - language (string): Язык результата (ru/en, по умолчанию ru)
     *   - division (string): Административное либо муниципальное деление (по умолчанию ADMINISTRATIVE)
     */
    public function reverseGeocode(float $latitude, float $longitude, array $options = []): DaDataSuggestResult|DaDataErrorResult
    {
        try {
            $requestData = [
                'lat' => $latitude,
                'lon' => $longitude,
                'count' => $options['count'] ?? 10,
            ];
            
            # Радиус поиска в метрах (максимум 1000)
            if (isset($options['radius_meters'])) {
                $requestData['radius_meters'] = min((int) $options['radius_meters'], 1000);
            }
            
            # Язык результата (ru/en)
            if (isset($options['language'])) {
                $requestData['language'] = $options['language'];
            }
            
            # Административное либо муниципальное деление
            if (isset($options['division'])) {
                $requestData['division'] = $options['division'];
            }
            
            $this->logRequest('reverseGeocode', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest('geolocate/address', $requestData),
                'reverseGeocode',
                ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options]
            );

            return new DaDataSuggestResult(
                $result,
                'DaData',
                'reverseGeocode',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new DaDataErrorResult(
                [],
                'DaData',
                'reverseGeocode',
                $e->getMessage(),
                GeofenceErrorType::DADATA_API_ERROR,
                $e->getCode(),
                ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Поиск адресов
     */
    public function suggest(string $query, int $count = 10, array $options = []): DaDataSuggestResult|DaDataErrorResult
    {
        try {
            $requestData = [
                'query' => $query,
                'count' => $count,
                'restrict_value' => $options['restrict_value'] ?? true,
                'locations' => $this->buildLocations($options),
            ];
            
            $this->logRequest('suggest', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest('suggest/address', $requestData),
                'suggest',
                ['query' => $query, 'count' => $count, 'options' => $options]
            );

            return new DaDataSuggestResult(
                $result,
                'DaData',
                'suggest',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new DaDataErrorResult(
                [],
                'DaData',
                'suggest',
                $e->getMessage(),
                GeofenceErrorType::DADATA_API_ERROR,
                $e->getCode(),
                ['query' => $query, 'count' => $count, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Валидация адреса
     */
    public function validate(string $address, array $options = []): DaDataSuggestResult|DaDataErrorResult
    {
        try {
            $requestData = [$address];
            
            $this->logRequest('validate', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest('clean/address', $requestData),
                'validate',
                ['address' => $address, 'options' => $options]
            );

            return new DaDataSuggestResult(
                $result,
                'DaData',
                'validate',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new DaDataErrorResult(
                [],
                'DaData',
                'validate',
                $e->getMessage(),
                GeofenceErrorType::DADATA_API_ERROR,
                $e->getCode(),
                ['address' => $address, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Поиск по координатам и улице
     */
    public function geolocateList(float $latitude, float $longitude, string $streetKladrId, string $house): DaDataSuggestResult|DaDataErrorResult
    {
        try {
            $requestData = [
                'lat' => $latitude,
                'lon' => $longitude,
            ];
            
            $this->logRequest('geolocateList', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest('geolocate/address', $requestData),
                'geolocateList',
                ['latitude' => $latitude, 'longitude' => $longitude, 'streetKladrId' => $streetKladrId, 'house' => $house]
            );

            return new DaDataSuggestResult(
                $result,
                'DaData',
                'geolocateList',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new DaDataErrorResult(
                [],
                'DaData',
                'geolocateList',
                $e->getMessage(),
                GeofenceErrorType::DADATA_API_ERROR,
                $e->getCode(),
                ['latitude' => $latitude, 'longitude' => $longitude, 'streetKladrId' => $streetKladrId, 'house' => $house],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Выполнение HTTP запроса
     */
    private function makeRequest(string $endpoint, array $data): array
    {
        try {
            $response = $this->httpClient->post($this->getApiUrl() . $endpoint, [
                'json' => $data,
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DaDataApiException(
                    'Invalid JSON response: ' . json_last_error_msg(),
                    'makeRequest',
                    ['endpoint' => $endpoint, 'data' => $data]
                );
            }

            # Проверяем на ошибки API
            if (isset($result['error'])) {
                $this->handleApiError($result['error'], $endpoint, $data);
            }

            return $result;

        } catch (GuzzleException $e) {
            $this->handleNetworkError($e, $endpoint, $data);
            # Этот код никогда не выполнится, так как handleNetworkError бросает исключение
            # Но добавляем для удовлетворения линтера
            throw $e;
        }
    }

    /**
     * Обработка ошибок API
     * @throws DaDataValidationException
     * @throws DaDataApiException
     * @throws DaDataQuotaException
     */
    private function handleApiError(array $error, string $endpoint, array $data): void
    {
        $errorCode = $error['code'] ?? null;
        $errorMessage = $error['message'] ?? 'Unknown API error';

        # Проверяем тип ошибки
        if ($errorCode === 'QUOTA_EXCEEDED') {
            throw new DaDataQuotaException(
                "DaData quota exceeded: {$errorMessage}",
                'makeRequest',
                ['endpoint' => $endpoint, 'data' => $data],
                $error['remaining_quota'] ?? null,
                $error['used_quota'] ?? null,
                $error['quota_reset_time'] ?? null
            );
        } elseif ($errorCode === 'VALIDATION_ERROR') {
            throw new DaDataValidationException(
                "DaData validation error: {$errorMessage}",
                'makeRequest',
                ['endpoint' => $endpoint, 'data' => $data],
                $error['field'] ?? null,
                $error['rule'] ?? null,
                $error['value'] ?? null
            );
        } else {
            throw new DaDataApiException(
                "DaData API error: {$errorMessage}",
                'makeRequest',
                ['endpoint' => $endpoint, 'data' => $data],
                $errorCode,
                $errorMessage
            );
        }
    }

    /**
     * Обработка сетевых ошибок
     * @throws DaDataNetworkException
     */
    private function handleNetworkError(GuzzleException $e, string $endpoint, array $data): void
    {
        $statusCode = null;
        $requestUrl = $this->getApiUrl() . $endpoint;

        if (method_exists($e, 'getResponse') && $e->getResponse()) {
            $statusCode = (string) $e->getResponse()->getStatusCode();
        }

        throw new DaDataNetworkException(
            "DaData network error: {$e->getMessage()}",
            'makeRequest',
            ['endpoint' => $endpoint, 'data' => $data],
            $statusCode,
            $requestUrl,
            $data,
            0,
            $e
        );
    }

    /**
     * Построение локаций для фильтрации
     */
    private function buildLocations(array $options): array
    {
        $locations = [];

        if ($this->getConfigValue('use_areas', false)) {
            $cityTitle = $options['city'] ?? $this->getConfigValue('default_city');
            $areas = $this->getAreasByCity($cityTitle);

            if (is_array($areas)) {
                foreach ($areas as $area) {
                    $locations[] = ['area' => $area];
                }
            } else {
                $locations[] = ['area' => $areas];
            }
        }

        if ($this->getConfigValue('use_radius', false)) {
            $locations[] = [
                'lat' => $this->getConfigValue('radius_center_lat', 51.559400),
                'lon' => $this->getConfigValue('radius_center_lng', 45.985774),
                'radius_meters' => $this->getConfigValue('radius_meters', 30000),
            ];

            foreach ($this->getConfigValue('settlements', []) as $settlement) {
                $locations[] = ['settlement' => $settlement];
            }

            $locations[] = ['region_with_type' => 'Саратовская обл'];
        }

        if (!empty($options['city'])) {
            $locations[] = ['city' => $options['city']];
        }

        return $locations;
    }

    /**
     * Получение областей по городу
     */
    private function getAreasByCity(string $city): array|string
    {
        $areas = [
            'Саратов' => ['Саратовский', 'Татищевский'],
            'Энгельс' => 'Энгельсский',
            'Казань' => '',
        ];

        return $areas[$city] ?? '';
    }

    /**
     * Проверить, включен ли сервис
     */
    public function isEnabled(): bool
    {
        return $this->getConfigValue('enabled', true);
    }
}
