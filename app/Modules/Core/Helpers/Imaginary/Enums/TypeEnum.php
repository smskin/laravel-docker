<?php

namespace App\Modules\Core\Helpers\Imaginary\Enums;

use App\Modules\Core\BaseEnum;
use App\Modules\Core\Models\EnumItem;
use Illuminate\Support\Collection;

class TypeEnum extends BaseEnum
{
    public const WEBP = 'webp';

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
