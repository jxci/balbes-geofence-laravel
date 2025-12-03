# balbes-geofence-laravel

Laravel-обёртка для работы с геосервисами (DaData + Yandex Geo) с fallback-логикой.

## Установка

```bash
composer require jxci/balbes-geofence-laravel
```

## Конфигурация

Опубликовать конфиг:

```bash
php artisan vendor:publish --provider=\"Jxci\\Geofence\\GeofenceServiceProvider\" --tag=config
```

Основные параметры в `config/geofence.php`:

- `default_service` — какой сервис использовать по умолчанию (`dadata` или `yandex_geo`).
- `fallback` — включение/отключение и выбор резервного сервиса.
- `dadata` / `yandex_geo` — base_uri, timeout, api_key и настройки логирования.

## Использование

Через фасад:

```php
use Geofence;

$result = Geofence::geocode('Саратов, Блинова 4Б');
```

Через DI:

```php
use Jxci\\Geofence\\GeofenceManager;

public function __construct(private GeofenceManager $geofence) {}
```


