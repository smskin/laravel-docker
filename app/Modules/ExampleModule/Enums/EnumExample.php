<?php

namespace App\Modules\ExampleModule\Enums;

use App\Modules\Core\BaseEnum;
use App\Modules\Core\Models\EnumItem;
use Illuminate\Support\Collection;
use function collect;

class EnumExample extends BaseEnum
{
    public const ITEM_1 = 'ITEM_1';
    public const ITEM_2 = 'ITEM_2';

    public static function items(): Collection
    {
        return collect(
            [
                (new EnumItem())
                    ->setId(self::ITEM_1),
                (new EnumItem())
                    ->setId(self::ITEM_2)
            ]
        );
    }
}
