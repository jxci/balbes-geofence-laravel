<?php

namespace App\Support\Geofence\Exceptions\YandexGeo;

/**
 * Исключение для ошибок Yandex Geo API
 */
class YandexGeoApiException extends YandexGeoException
{
    protected readonly ?string $apiErrorCode;
    protected readonly ?string $apiErrorMessage;
    protected readonly ?string $apiErrorDetails;

    public function __construct(
        string $message,
        string $method,
        array $context = [],
        ?string $apiErrorCode = null,
        ?string $apiErrorMessage = null,
        ?string $apiErrorDetails = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $method, $context, $code, $previous);
        
        $this->apiErrorCode = $apiErrorCode;
        $this->apiErrorMessage = $apiErrorMessage;
        $this->apiErrorDetails = $apiErrorDetails;
    }

    public function getErrorType(): string
    {
        return 'Yandex Geo API Error';
    }

    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    public function getApiErrorMessage(): ?string
    {
        return $this->apiErrorMessage;
    }

    public function getApiErrorDetails(): ?string
    {
        return $this->apiErrorDetails;
    }

    public function getRecommendations(): array
    {
        $recommendations = parent::getRecommendations();
        
        if ($this->apiErrorCode) {
            $recommendations[] = "API Error Code: {$this->apiErrorCode}";
        }
        
        if ($this->apiErrorMessage) {
            $recommendations[] = "API Error Message: {$this->apiErrorMessage}";
        }

        if ($this->apiErrorDetails) {
            $recommendations[] = "API Error Details: {$this->apiErrorDetails}";
        }

        return $recommendations;
    }
}
