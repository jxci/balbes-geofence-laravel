<?php

namespace App\Support\Geofence;

use App\Support\Geofence\Contracts\CorrelationContextInterface;
use App\Support\Geofence\Contracts\LoggerContextInterface;
use App\Support\Geofence\Results\BaseResult;
use App\Support\Geofence\Traits\CorrelationContextTrait;
use App\Support\Geofence\Traits\LoggerContextTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

/**
 * Абстрактный базовый класс для геосервисов с поддержкой Result объектов
 */
abstract class AbstractGeoService implements CorrelationContextInterface, LoggerContextInterface
{
    use CorrelationContextTrait, LoggerContextTrait;

    protected readonly array $config;
    protected readonly Client $httpClient;
    protected readonly string $serviceName;

    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->config = Config::get('geofence.' . strtolower($serviceName));

        if (!$this->config) {
            throw new \InvalidArgumentException("Configuration for {$serviceName} service not found.");
        }

        $this->httpClient = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout' => $this->config['timeout'],
            'headers' => $this->getHeaders(),
        ]);
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
    protected function logRequest(string $method, array $data): void
    {
        if ($this->getConfigValue('logging.log_requests', true) && $this->hasLogger()) {
            $this->logInfo("{$this->serviceName}@{$method} Request", [
                'service' => $this->serviceName,
                'method' => $method,
                'data' => $data,
                'correlation_id' => $this->getCorrelationId(),
            ]);
        }
    }

    /**
     * Логирование ответа
     */
    protected function logResponse(string $method, array $response): void
    {
        if ($this->getConfigValue('logging.log_responses', true) && $this->hasLogger()) {
            $this->logInfo("{$this->serviceName}@{$method} Response", [
                'service' => $this->serviceName,
                'method' => $method,
                'response' => $response,
                'correlation_id' => $this->getCorrelationId(),
            ]);
        }
    }

    /**
     * Логирование ошибки
     */
    protected function logError(string $method, string $message, array $context = []): void
    {
        if ($this->getConfigValue('logging.log_errors', true) && $this->hasLogger()) {
            $this->log('error', "{$this->serviceName}@{$method} Error", [
                'service' => $this->serviceName,
                'method' => $method,
                'message' => $message,
                'context' => $context,
                'correlation_id' => $this->getCorrelationId(),
            ]);
        }
    }

    /**
     * Выполнение запроса с повторными попытками
     */
    protected function executeWithRetry(callable $callback, string $method, array $context = []): array
    {
        $maxAttempts = $this->getConfigValue('retry_attempts', 3);
        $retryDelay = $this->getConfigValue('retry_delay', 100);

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $result = $callback();
                $this->logResponse($method, $result);
                return $result;
            } catch (ConnectException $e) {
                $this->logError($method, "Connection error (attempt {$attempt}): {$e->getMessage()}", $context);
                
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
                
                usleep($retryDelay * 1000 * $attempt);
            } catch (RequestException $e) {
                $this->logError($method, "Request error (attempt {$attempt}): {$e->getMessage()}", $context);
                
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
                
                usleep($retryDelay * 1000 * $attempt);
            } catch (\Exception $e) {
                $this->logError($method, "Unexpected error (attempt {$attempt}): {$e->getMessage()}", $context);
                throw $e;
            }
        }

        throw new \RuntimeException("Max retry attempts exceeded for {$method}");
    }

    /**
     * Абстрактные методы для получения заголовков
     */
    abstract protected function getHeaders(): array;

    /**
     * Абстрактные методы для геокодирования
     */
    abstract public function geocode(string $address, array $options = []): BaseResult;
    abstract public function reverseGeocode(float $latitude, float $longitude, array $options = []): BaseResult;
    abstract public function suggest(string $query, int $count = 10, array $options = []): BaseResult;
    abstract public function validate(string $address, array $options = []): BaseResult;
}
