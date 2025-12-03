<?php

namespace App\Support\Geofence\Exceptions\DaData;

/**
 * Исключение для сетевых ошибок DaData
 */
class DaDataNetworkException extends DaDataException
{
    protected readonly ?string $httpStatusCode;
    protected readonly ?string $requestUrl;
    protected readonly ?array $requestData;

    public function __construct(
        string $message,
        string $method,
        array $context = [],
        ?string $httpStatusCode = null,
        ?string $requestUrl = null,
        ?array $requestData = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $method, $context, $code, $previous);
        
        $this->httpStatusCode = $httpStatusCode;
        $this->requestUrl = $requestUrl;
        $this->requestData = $requestData;
    }

    public function getErrorType(): string
    {
        return 'DaData Network Error';
    }

    public function getHttpStatusCode(): ?string
    {
        return $this->httpStatusCode;
    }

    public function getRequestUrl(): ?string
    {
        return $this->requestUrl;
    }

    public function getRequestData(): ?array
    {
        return $this->requestData;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Проверьте подключение к интернету',
            'Убедитесь, что DaData API доступен',
            'Проверьте настройки прокси и файрвола',
        ];

        if ($this->httpStatusCode) {
            $recommendations[] = "HTTP Status Code: {$this->httpStatusCode}";
        }

        if ($this->httpStatusCode === '401') {
            $recommendations[] = 'Проверьте корректность API токена';
        } elseif ($this->httpStatusCode === '403') {
            $recommendations[] = 'Проверьте права доступа к API';
        } elseif ($this->httpStatusCode === '429') {
            $recommendations[] = 'Превышен лимит запросов, попробуйте позже';
        } elseif ($this->httpStatusCode === '500') {
            $recommendations[] = 'Ошибка на стороне DaData, попробуйте позже';
        }

        return $recommendations;
    }
}
