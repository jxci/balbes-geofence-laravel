<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Уровень детализации ФИАС (fias_level)
 */
enum FiasLevel: string
{
    case COUNTRY = '0';
    case REGION = '1';
    case AREA = '3';
    case CITY = '4';
    case CITY_DISTRICT = '5';
    case SETTLEMENT = '6';
    case STREET = '7';
    case HOUSE = '8';
    case FLAT = '9';
    case PLANNING_STRUCTURE = '65';
    case ADDITIONAL_TERRITORY = '90';
    case STREET_IN_ADDITIONAL_TERRITORY = '91';
    case FOREIGN_OR_EMPTY = '-1';

    public function getDescription(): string
    {
        return match ($this) {
            self::COUNTRY => 'Страна',
            self::REGION => 'Регион',
            self::AREA => 'Район',
            self::CITY => 'Город',
            self::CITY_DISTRICT => 'Район города',
            self::SETTLEMENT => 'Населенный пункт',
            self::STREET => 'Улица',
            self::HOUSE => 'Дом',
            self::FLAT => 'Квартира',
            self::PLANNING_STRUCTURE => 'Планировочная структура',
            self::ADDITIONAL_TERRITORY => 'Дополнительная территория',
            self::STREET_IN_ADDITIONAL_TERRITORY => 'Улица в дополнительной территории',
            self::FOREIGN_OR_EMPTY => 'Иностранный или пустой',
        };
    }

    public function isAddressable(): bool
    {
        return in_array($this, [
            self::HOUSE,
            self::FLAT,
            self::STREET,
            self::SETTLEMENT,
        ], true);
    }

    public static function fromString(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

