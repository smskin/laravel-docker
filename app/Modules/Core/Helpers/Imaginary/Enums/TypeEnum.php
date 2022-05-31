<?php

namespace App\Modules\Core\Helpers\Imaginary\Enums;

use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;
use SMSkin\LaravelSupport\Models\EnumItem;

class TypeEnum extends BaseEnum
{
    public const WEBP = 'webp';

    /**
     * @return Collection<EnumItem>
     */
    public static function items(): Collection
    {
        return collect(
            [
                (new EnumItem())
                    ->setId(self::WEBP)
                    ->setTitle('webp')
            ]
        );
    }
}
