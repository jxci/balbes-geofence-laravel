<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Код пригодности к рассылке (qc_complete)
 */
enum AddressCompletenessCode: int
{
    case SUITABLE = 0;
    case NO_REGION = 1;
    case NO_CITY = 2;
    case NO_STREET = 3;
    case NO_HOUSE = 4;
    case NO_FLAT = 5;
    case INCOMPLETE = 6;
    case FOREIGN = 7;
    case POSTAL_BOX = 8;
    case NEEDS_CHECK = 9;
    case NO_HOUSE_IN_FIAS = 10;

    public function getDescription(): string
    {
        return match ($this) {
            self::SUITABLE => 'Пригоден для почтовой рассылки',
            self::NO_REGION => 'Нет региона',
            self::NO_CITY => 'Нет города',
            self::NO_STREET => 'Нет улицы',
            self::NO_HOUSE => 'Нет дома',
            self::NO_FLAT => 'Нет квартиры',
            self::INCOMPLETE => 'Адрес неполный',
            self::FOREIGN => 'Иностранный адрес',
            self::POSTAL_BOX => 'Абонентский ящик или адрес до востребования',
            self::NEEDS_CHECK => 'Сначала проверьте правильность разбора адреса',
            self::NO_HOUSE_IN_FIAS => 'Дома нет в ФИАС',
        };
    }

    public function isSuitableForDelivery(): bool
    {
        return $this === self::SUITABLE;
    }

    public function isQuestionable(): bool
    {
        return in_array($this, [self::NO_FLAT, self::POSTAL_BOX, self::NEEDS_CHECK, self::NO_HOUSE_IN_FIAS], true);
    }

    public function isUnsuitable(): bool
    {
        return in_array($this, [
            self::NO_REGION,
            self::NO_CITY,
            self::NO_STREET,
            self::NO_HOUSE,
            self::INCOMPLETE,
            self::FOREIGN,
        ], true);
    }

    public static function fromInt(?int $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

