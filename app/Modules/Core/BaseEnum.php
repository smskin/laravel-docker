<?php

namespace App\Modules\Core;

use App\Modules\Core\Models\EnumItem;
use Illuminate\Support\Collection;

abstract class BaseEnum
{
    /**
     * @return Collection
     */
    abstract public static function items(): Collection;

    /**
     * @return string[]
     */
    public static function getKeys(): array
    {
        return static::items()->pluck('id')->toArray();
    }

    public static function toNovaSelectorOptions(): array
    {
        return static::items()->mapWithKeys(function (EnumItem $item) {
            return [$item->id => $item->title];
        })->toArray();
    }
}
