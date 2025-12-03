<?php

namespace App\Support\Geofence\Exceptions\YandexGeo;

/**
 * Исключение для сетевых ошибок Yandex Geo
 */
class YandexGeoNetworkException extends YandexGeoException
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
        return 'Yandex Geo Network Error';
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
            'Убедитесь, что Yandex Geo API доступен',
            'Проверьте настройки прокси и файрвола',
        ];

        if ($this->httpStatusCode) {
            $recommendations[] = "HTTP Status Code: {$this->httpStatusCode}";
        }

        if ($this->httpStatusCode === '401') {
            $recommendations[] = 'Проверьте корректность API ключа Yandex';
        } elseif ($this->httpStatusCode === '403') {
            $recommendations[] = 'Проверьте права доступа к Yandex Geo API';
        } elseif ($this->httpStatusCode === '429') {
            $recommendations[] = 'Превышен лимит запросов к Yandex Geo, попробуйте позже';
        } elseif ($this->httpStatusCode === '500') {
            $recommendations[] = 'Ошибка на стороне Yandex Geo, попробуйте позже';
        }

        return $recommendations;
    }
}
