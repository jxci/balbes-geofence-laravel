<?php

namespace App\Support\Geofence\YandexGeo;

use App\Support\Geofence\AbstractGeoService;
use App\Support\Geofence\Enums\GeofenceErrorType;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoApiException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoNetworkException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoQuotaException;
use App\Support\Geofence\Exceptions\YandexGeo\YandexGeoValidationException;
use App\Support\Geofence\YandexGeo\Enums\YandexGeoLanguage;
use App\Support\Geofence\YandexGeo\Results\YandexGeoErrorResult;
use App\Support\Geofence\YandexGeo\Results\YandexGeoGeocodeResult;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Yandex Geo API сервис
 */
class YandexGeoService extends AbstractGeoService
{
    private readonly string $apiUrl;
    private readonly string $apiKey;
    private readonly string $version;
    private readonly YandexGeoLanguage $language;
    private readonly int $limit;
    private readonly int $offset;

    public function __construct()
    {
        parent::__construct('yandex_geo');

        $this->apiUrl = $this->getConfigValue('base_uri');
        $this->apiKey = $this->getConfigValue('api_key');
        $this->version = $this->getConfigValue('version', '1.x');
        
        # Инициализация языка с fallback на enum
        $languageString = $this->getConfigValue('language', 'ru-RU');
        $this->language = YandexGeoLanguage::isSupported($languageString) 
            ? YandexGeoLanguage::fromString($languageString)
            : YandexGeoLanguage::getDefault();
            
        $this->limit = $this->getConfigValue('limit', 10);
        $this->offset = $this->getConfigValue('offset', 0);
    }

    protected function getHeaders(): array
    {
        return []; # Yandex Geo API key is passed as a query parameter
    }

    /**
     * Геокодирование адреса
     */
    public function geocode(string $address, array $options = []): YandexGeoGeocodeResult|YandexGeoErrorResult
    {
        try {
            $requestData = [
                'geocode' => $address,
                'format' => 'json',
                'results' => $options['count'] ?? $this->limit,
                'skip' => $options['offset'] ?? $this->offset,
                'lang' => $options['language'] ?? $this->language->value,
            ];
            
            $this->logRequest('geocode', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest($requestData),
                'geocode',
                ['address' => $address, 'options' => $options]
            );

            return new YandexGeoGeocodeResult(
                $result,
                'YandexGeo',
                'geocode',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new YandexGeoErrorResult(
                [],
                'YandexGeo',
                'geocode',
                $e->getMessage(),
                GeofenceErrorType::YANDEX_GEO_API_ERROR,
                $e->getCode(),
                ['address' => $address, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Обратное геокодирование
     */
    public function reverseGeocode(float $latitude, float $longitude, array $options = []): YandexGeoGeocodeResult|YandexGeoErrorResult
    {
        try {
            $requestData = [
                'geocode' => sprintf('%F,%F', $longitude, $latitude),
                'format' => 'json',
                'results' => $options['count'] ?? $this->limit,
                'skip' => $options['offset'] ?? $this->offset,
                'lang' => $options['language'] ?? $this->language->value,
            ];
            
            $this->logRequest('reverseGeocode', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest($requestData),
                'reverseGeocode',
                ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options]
            );

            return new YandexGeoGeocodeResult(
                $result,
                'YandexGeo',
                'reverseGeocode',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new YandexGeoErrorResult(
                [],
                'YandexGeo',
                'reverseGeocode',
                $e->getMessage(),
                GeofenceErrorType::YANDEX_GEO_API_ERROR,
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
    public function suggest(string $query, int $count = 10, array $options = []): YandexGeoGeocodeResult|YandexGeoErrorResult
    {
        try {
            $requestData = [
                'geocode' => $query,
                'format' => 'json',
                'results' => $count,
                'skip' => $options['offset'] ?? $this->offset,
                'lang' => $options['language'] ?? $this->language->value,
            ];
            
            $this->logRequest('suggest', $requestData);
            
            $result = $this->executeWithRetry(
                fn() => $this->makeRequest($requestData),
                'suggest',
                ['query' => $query, 'count' => $count, 'options' => $options]
            );

            return new YandexGeoGeocodeResult(
                $result,
                'YandexGeo',
                'suggest',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new YandexGeoErrorResult(
                [],
                'YandexGeo',
                'suggest',
                $e->getMessage(),
                GeofenceErrorType::YANDEX_GEO_API_ERROR,
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
    public function validate(string $address, array $options = []): YandexGeoGeocodeResult|YandexGeoErrorResult
    {
        # Yandex Geo не имеет отдельного метода валидации
        # Используем геокодирование для проверки
        return $this->geocode($address, $options);
    }

    /**
     * Поиск по координатам с фильтрацией
     */
    public function searchByCoordinates(float $latitude, float $longitude, array $options = []): YandexGeoGeocodeResult|YandexGeoErrorResult
    {
        try {
            $requestData = [
                'geocode' => sprintf('%F,%F', $longitude, $latitude),
                'format' => 'json',
                'results' => $options['count'] ?? $this->limit,
                'skip' => $options['offset'] ?? $this->offset,
                'lang' => $options['language'] ?? $this->language->value,
            ];

            # Добавляем ограничения по области если нужно
            if ($this->getConfigValue('use_area_limit', false) && isset($options['area'])) {
                $requestData['spn'] = sprintf('%f,%f', $options['area']['lengthLng'], $options['area']['lengthLat']);
                $requestData['ll'] = sprintf('%f,%f', $options['area']['longitude'], $options['area']['latitude']);
                $requestData['rspn'] = 1;
            }

            $this->logRequest('searchByCoordinates', $requestData);

            $result = $this->executeWithRetry(
                fn() => $this->makeRequest($requestData),
                'searchByCoordinates',
                ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options]
            );

            return new YandexGeoGeocodeResult(
                $result,
                'YandexGeo',
                'searchByCoordinates',
                $this->getCorrelationId()
            );
        } catch (\Exception $e) {
            return new YandexGeoErrorResult(
                [],
                'YandexGeo',
                'searchByCoordinates',
                $e->getMessage(),
                GeofenceErrorType::YANDEX_GEO_API_ERROR,
                $e->getCode(),
                ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options],
                $e,
                $this->getCorrelationId()
            );
        }
    }

    /**
     * Получить текущий язык
     */
    public function getLanguage(): YandexGeoLanguage
    {
        return $this->language;
    }

    /**
     * Получить все поддерживаемые языки
     */
    public function getSupportedLanguages(): array
    {
        return YandexGeoLanguage::getAll();
    }

    /**
     * Проверить, поддерживается ли язык
     */
    public function isLanguageSupported(string $language): bool
    {
        return YandexGeoLanguage::isSupported($language);
    }

    /**
     * Проверка координат на fallback значения
     */
    public function isFallbackCoordinates(float $latitude, float $longitude): bool
    {
        $fallbackLat = (float) $this->getConfigValue('fallback_coordinates.lat', '51.533');
        $fallbackLng = (float) $this->getConfigValue('fallback_coordinates.lng', '46.034');

        return (stripos((string)$latitude, (string)$fallbackLat) === 0 &&
                stripos((string)$longitude, (string)$fallbackLng) === 0);
    }

    /**
     * Выполнение HTTP запроса
     */
    private function makeRequest(array $params): array
    {
        try {
            # API ключ передается в URL параметрах
            $params['apikey'] = $this->getConfigValue('api_key');
            $url = $this->apiUrl . $this->version . '/?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

            $response = $this->httpClient->get($url);
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new YandexGeoApiException(
                    'Invalid JSON response: ' . json_last_error_msg(),
                    'makeRequest',
                    ['params' => $params, 'url' => $url]
                );
            }

            if (!empty($result['error'])) {
                $this->handleApiError($result['error'], $params, $url);
            }

            return $result;

        } catch (GuzzleException $e) {
            $this->handleNetworkError($e, $params, $url);
            throw $e; # Re-throw for linter satisfaction
        }
    }

    /**
     * Обработка ошибок API
     */
    private function handleApiError(array $error, array $params, string $url): void
    {
        $errorCode = $error['code'] ?? null;
        $errorMessage = $error['message'] ?? 'Unknown API error';
        $errorDetails = $error['details'] ?? null;

        # Проверяем тип ошибки
        if ($errorCode === 'QUOTA_EXCEEDED') {
            throw new YandexGeoQuotaException(
                "Yandex Geo quota exceeded: {$errorMessage}",
                'makeRequest',
                ['params' => $params, 'url' => $url],
                $error['remaining_quota'] ?? null,
                $error['used_quota'] ?? null,
                $error['quota_reset_time'] ?? null
            );
        } elseif ($errorCode === 'VALIDATION_ERROR') {
            throw new YandexGeoValidationException(
                "Yandex Geo validation error: {$errorMessage}",
                'makeRequest',
                ['params' => $params, 'url' => $url],
                $error['field'] ?? null,
                $error['rule'] ?? null,
                $error['value'] ?? null
            );
        } else {
            throw new YandexGeoApiException(
                "Yandex Geo API error: {$errorMessage}",
                'makeRequest',
                ['params' => $params, 'url' => $url],
                $errorCode,
                $errorMessage,
                $errorDetails
            );
        }
    }

    /**
     * Обработка сетевых ошибок
     */
    private function handleNetworkError(GuzzleException $e, array $params, string $url): void
    {
        $statusCode = null;

        if (method_exists($e, 'getResponse') && $e->getResponse()) {
            $statusCode = (string) $e->getResponse()->getStatusCode();
        }

        throw new YandexGeoNetworkException(
            "Yandex Geo network error: {$e->getMessage()}",
            'makeRequest',
            ['params' => $params, 'url' => $url],
            $statusCode,
            $url,
            $params,
            0,
            $e
        );
    }

    /**
     * Проверить, включен ли сервис
     */
    public function isEnabled(): bool
    {
        return $this->getConfigValue('enabled', true);
    }
}
