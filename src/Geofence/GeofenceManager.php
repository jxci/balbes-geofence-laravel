<?php

namespace App\Support\Geofence;

use App\Support\Geofence\DaData\DaDataService;
use App\Support\Geofence\Enums\GeofenceServiceType;
use App\Support\Geofence\Exceptions\GeofenceFallbackException;
use App\Support\Geofence\Results\BaseResult;
use App\Support\Geofence\YandexGeo\YandexGeoService;
use Illuminate\Support\Facades\Log;

class GeofenceManager
{
    private readonly DaDataService $daDataService;
    private readonly YandexGeoService $yandexGeoService;
    private readonly array $config;
    private GeofenceServiceType $currentService;

    public function __construct(
        DaDataService $daDataService,
        YandexGeoService $yandexGeoService,
        array $config
    ) {
        $this->daDataService = $daDataService;
        $this->yandexGeoService = $yandexGeoService;
        $this->config = $config;
        $this->currentService = GeofenceServiceType::from($config['default_service'] ?? 'dadata');
    }

    /**
     * Установить сервис для использования
     */
    public function use(GeofenceServiceType $service): self
    {
        $this->currentService = $service;
        return $this;
    }

    /**
     * Использовать DaData
     */
    public function daData(): self
    {
        return $this->use(GeofenceServiceType::DADATA);
    }

    /**
     * Использовать Yandex Geo
     */
    public function yandexGeo(): self
    {
        return $this->use(GeofenceServiceType::YANDEX_GEO);
    }

    /**
     * Геокодирование с fallback
     */
    public function geocode(string $address, array $options = []): BaseResult
    {
        return $this->executeWithFallback(
            fn($service) => $service->geocode($address, $options),
            'geocode',
            ['address' => $address, 'options' => $options]
        );
    }

    /**
     * Обратное геокодирование с fallback
     */
    public function reverseGeocode(float $latitude, float $longitude, array $options = []): BaseResult
    {
        return $this->executeWithFallback(
            fn($service) => $service->reverseGeocode($latitude, $longitude, $options),
            'reverseGeocode',
            ['latitude' => $latitude, 'longitude' => $longitude, 'options' => $options]
        );
    }

    /**
     * Поиск адресов с fallback
     */
    public function suggest(string $query, int $count = 10, array $options = []): BaseResult
    {
        return $this->executeWithFallback(
            fn($service) => $service->suggest($query, $count, $options),
            'suggest',
            ['query' => $query, 'count' => $count, 'options' => $options]
        );
    }

    /**
     * Валидация адреса с fallback
     */
    public function validate(string $address, array $options = []): BaseResult
    {
        return $this->executeWithFallback(
            fn($service) => $service->validate($address, $options),
            'validate',
            ['address' => $address, 'options' => $options]
        );
    }

    /**
     * Выполнение с fallback логикой
     */
    private function executeWithFallback(callable $callback, string $method, array $context = []): BaseResult
    {
        $primaryService = $this->getCurrentService();
        $fallbackService = $this->getFallbackService();
        
        try {
            return $callback($primaryService);
        } catch (\Exception $e) {
            if ($this->config['fallback']['enabled'] ?? true) {
                Log::warning("Primary service failed, using fallback", [
                    'method' => $method,
                    'primary_service' => $primaryService::class,
                    'fallback_service' => $fallbackService::class,
                    'error' => $e->getMessage(),
                ]);
                
                try {
                    return $callback($fallbackService);
                } catch (\Exception $fallbackException) {
                    throw new GeofenceFallbackException(
                        'Both geofence services failed',
                        'GeofenceManager',
                        $method,
                        $context,
                        $primaryService::class,
                        $fallbackService::class,
                        ['error' => $e->getMessage()],
                        ['error' => $fallbackException->getMessage()]
                    );
                }
            }
            
            throw $e;
        }
    }

    /**
     * Получить текущий сервис
     */
    private function getCurrentService(): DaDataService|YandexGeoService
    {
        return match ($this->currentService) {
            GeofenceServiceType::DADATA => $this->daDataService,
            GeofenceServiceType::YANDEX_GEO => $this->yandexGeoService,
        };
    }

    /**
     * Получить резервный сервис
     */
    private function getFallbackService(): DaDataService|YandexGeoService
    {
        $fallbackService = GeofenceServiceType::from($this->config['fallback']['service'] ?? 'yandex_geo');
        
        return match ($fallbackService) {
            GeofenceServiceType::DADATA => $this->daDataService,
            GeofenceServiceType::YANDEX_GEO => $this->yandexGeoService,
        };
    }
}
