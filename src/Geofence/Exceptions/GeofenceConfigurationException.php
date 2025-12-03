<?php

namespace App\Support\Geofence\Exceptions;

/**
 * Исключение для ошибок конфигурации геосервисов
 */
class GeofenceConfigurationException extends GeofenceException
{
    protected readonly ?string $configKey;
    protected readonly ?string $expectedValue;
    protected readonly ?string $actualValue;

    public function __construct(
        string $message,
        string $serviceName,
        string $method,
        array $context = [],
        ?string $configKey = null,
        ?string $expectedValue = null,
        ?string $actualValue = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $serviceName, $method, $context, $code, $previous);
        
        $this->configKey = $configKey;
        $this->expectedValue = $expectedValue;
        $this->actualValue = $actualValue;
    }

    public function getErrorType(): string
    {
        return 'Geofence Configuration Error';
    }

    public function getConfigKey(): ?string
    {
        return $this->configKey;
    }

    public function getExpectedValue(): ?string
    {
        return $this->expectedValue;
    }

    public function getActualValue(): ?string
    {
        return $this->actualValue;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Проверьте конфигурацию геосервисов',
            'Убедитесь, что все необходимые параметры заданы',
            'Проверьте переменные окружения',
        ];

        if ($this->configKey) {
            $recommendations[] = "Проблемный параметр: {$this->configKey}";
        }

        if ($this->expectedValue) {
            $recommendations[] = "Ожидаемое значение: {$this->expectedValue}";
        }

        if ($this->actualValue) {
            $recommendations[] = "Текущее значение: {$this->actualValue}";
        }

        return $recommendations;
    }
}
