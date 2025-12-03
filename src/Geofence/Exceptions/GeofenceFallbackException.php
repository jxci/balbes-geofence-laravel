<?php

namespace App\Support\Geofence\Exceptions;

/**
 * Исключение для ошибок fallback логики
 */
class GeofenceFallbackException extends GeofenceException
{
    protected readonly ?string $primaryService;
    protected readonly ?string $fallbackService;
    protected readonly ?array $primaryError;
    protected readonly ?array $fallbackError;

    public function __construct(
        string $message,
        string $serviceName,
        string $method,
        array $context = [],
        ?string $primaryService = null,
        ?string $fallbackService = null,
        ?array $primaryError = null,
        ?array $fallbackError = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $serviceName, $method, $context, $code, $previous);
        
        $this->primaryService = $primaryService;
        $this->fallbackService = $fallbackService;
        $this->primaryError = $primaryError;
        $this->fallbackError = $fallbackError;
    }

    public function getErrorType(): string
    {
        return 'Geofence Fallback Error';
    }

    public function getPrimaryService(): ?string
    {
        return $this->primaryService;
    }

    public function getFallbackService(): ?string
    {
        return $this->fallbackService;
    }

    public function getPrimaryError(): ?array
    {
        return $this->primaryError;
    }

    public function getFallbackError(): ?array
    {
        return $this->fallbackError;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Все геосервисы недоступны',
            'Проверьте подключение к интернету',
            'Проверьте конфигурацию всех сервисов',
        ];

        if ($this->primaryService) {
            $recommendations[] = "Основной сервис: {$this->primaryService}";
        }

        if ($this->fallbackService) {
            $recommendations[] = "Резервный сервис: {$this->fallbackService}";
        }

        if ($this->primaryError) {
            $recommendations[] = "Ошибка основного сервиса: " . json_encode($this->primaryError);
        }

        if ($this->fallbackError) {
            $recommendations[] = "Ошибка резервного сервиса: " . json_encode($this->fallbackError);
        }

        return $recommendations;
    }
}
