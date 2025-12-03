<?php

namespace App\Support\Geofence;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Абстрактный класс для геосервисов
 */
abstract class AbstractGeoService
{
    protected readonly LoggerInterface $logger;
    protected readonly array $config;
    protected readonly bool $enabled;
    protected readonly int $timeout;
    protected readonly int $retryAttempts;
    protected readonly int $retryDelay;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->enabled = $config['enabled'] ?? true;
        $this->timeout = $config['timeout'] ?? 10;
        $this->retryAttempts = $config['retry_attempts'] ?? 3;
        $this->retryDelay = $config['retry_delay'] ?? 1;
        
        $this->logger = Log::channel('geofence');
    }

    /**
     * Проверка доступности сервиса
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Получение конфигурации
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Получение значения из конфигурации
     */
    protected function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Логирование запроса
     */
    protected function logRequest(string $method, array $data, ?string $correlationId = null): void
    {
        if (!$this->getConfigValue('logging.log_requests', true)) {
            return;
        }

        $this->logger->info("[{$this->getServiceName()}@{$method}] Запрос к геосервису", [
            'correlation_id' => $correlationId ?? uniqid('geo_', true),
            'service' => $this->getServiceName(),
            'method' => $method,
            'data' => $data,
        ]);
    }

    /**
     * Логирование ответа
     */
    protected function logResponse(string $method, mixed $response, ?string $correlationId = null): void
    {
        if (!$this->getConfigValue('logging.log_responses', false)) {
            return;
        }

        $this->logger->info("[{$this->getServiceName()}@{$method}] Ответ от геосервиса", [
            'correlation_id' => $correlationId ?? uniqid('geo_', true),
            'service' => $this->getServiceName(),
            'method' => $method,
            'response' => $response,
        ]);
    }

    /**
     * Логирование ошибки
     */
    protected function logError(string $method, \Exception $exception, ?string $correlationId = null): void
    {
        if (!$this->getConfigValue('logging.log_errors', true)) {
            return;
        }

        $this->logger->error("[{$this->getServiceName()}@{$method}] Ошибка геосервиса", [
            'correlation_id' => $correlationId ?? uniqid('geo_', true),
            'service' => $this->getServiceName(),
            'method' => $method,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Выполнение запроса с retry логикой
     */
    protected function executeWithRetry(callable $callback, string $method, array $data = [], ?string $correlationId = null): mixed
    {
        $correlationId = $correlationId ?? uniqid('geo_', true);
        
        $this->logRequest($method, $data, $correlationId);

        $lastException = null;
        
        for ($attempt = 1; $attempt <= $this->retryAttempts; $attempt++) {
            try {
                $result = $callback();
                $this->logResponse($method, $result, $correlationId);
                return $result;
                
            } catch (\Exception $exception) {
                $lastException = $exception;
                $this->logError($method, $exception, $correlationId);
                
                if ($attempt < $this->retryAttempts) {
                    sleep($this->retryDelay);
                }
            }
        }

        throw $lastException;
    }

    /**
     * Получение имени сервиса
     */
    abstract protected function getServiceName(): string;

    /**
     * Геокодирование адреса
     */
    abstract public function geocode(string $address, array $options = []): array;

    /**
     * Обратное геокодирование (координаты -> адрес)
     */
    abstract public function reverseGeocode(float $latitude, float $longitude, array $options = []): array;

    /**
     * Поиск адресов
     */
    abstract public function suggest(string $query, int $count = 10, array $options = []): array;

    /**
     * Валидация адреса
     */
    abstract public function validate(string $address, array $options = []): array;
}
