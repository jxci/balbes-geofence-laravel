<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Признак нахождения внутри кольцевой дороги (beltway_hit)
 */
enum BeltwayHit: string
{
    case IN_MKAD = 'IN_MKAD';
    case OUT_MKAD = 'OUT_MKAD';
    case IN_KAD = 'IN_KAD';
    case OUT_KAD = 'OUT_KAD';

    public function getDescription(): string
    {
        return match ($this) {
            self::IN_MKAD => 'Внутри МКАД (Москва)',
            self::OUT_MKAD => 'За МКАД (Москва и область)',
            self::IN_KAD => 'Внутри КАД (Санкт-Петербург)',
            self::OUT_KAD => 'За КАД (Санкт-Петербург и область)',
        };
    }

    public function isInside(): bool
    {
        return in_array($this, [self::IN_MKAD, self::IN_KAD], true);
    }

    public function isMoscow(): bool
    {
        return in_array($this, [self::IN_MKAD, self::OUT_MKAD], true);
    }

    public function isSaintPetersburg(): bool
    {
        return in_array($this, [self::IN_KAD, self::OUT_KAD], true);
    }

    public static function fromString(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

