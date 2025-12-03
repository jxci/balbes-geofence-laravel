<?php

namespace App\Support\Geofence\YandexGeo\Enums;

/**
 * Языки для Yandex Geo API
 * 
 * Основано на yandex-php/php-yandex-geo
 * @see https://github.com/yandex-php/php-yandex-geo
 */
enum YandexGeoLanguage: string
{
    /** русский (по умолчанию) */
    case RU = 'ru-RU';
    
    /** украинский */
    case UA = 'uk-UA';
    
    /** белорусский */
    case BY = 'be-BY';
    
    /** американский английский */
    case US = 'en-US';
    
    /** британский английский */
    case BR = 'en-BR';
    
    /** турецкий (только для карты Турции) */
    case TR = 'tr-TR';

    /**
     * Получить язык по умолчанию
     */
    public static function getDefault(): self
    {
        return self::RU;
    }

    /**
     * Получить все доступные языки
     */
    public static function getAll(): array
    {
        return [
            self::RU,
            self::UA,
            self::BY,
            self::US,
            self::BR,
            self::TR,
        ];
    }

    /**
     * Получить языки в виде массива
     */
    public static function toArray(): array
    {
        return array_map(fn($lang) => $lang->value, self::getAll());
    }

    /**
     * Проверить, поддерживается ли язык
     */
    public static function isSupported(string $language): bool
    {
        return in_array($language, self::toArray());
    }

    /**
     * Получить язык по строке
     */
    public static function fromString(string $language): self
    {
        return self::from($language);
    }

    /**
     * Получить человекочитаемое название языка
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::RU => 'Русский',
            self::UA => 'Украинский',
            self::BY => 'Белорусский',
            self::US => 'Американский английский',
            self::BR => 'Британский английский',
            self::TR => 'Турецкий',
        };
    }

    /**
     * Получить код страны
     */
    public function getCountryCode(): string
    {
        return match($this) {
            self::RU => 'RU',
            self::UA => 'UA',
            self::BY => 'BY',
            self::US => 'US',
            self::BR => 'GB',
            self::TR => 'TR',
        };
    }

    /**
     * Получить код языка
     */
    public function getLanguageCode(): string
    {
        return match($this) {
            self::RU => 'ru',
            self::UA => 'uk',
            self::BY => 'be',
            self::US => 'en',
            self::BR => 'en',
            self::TR => 'tr',
        };
    }
}
