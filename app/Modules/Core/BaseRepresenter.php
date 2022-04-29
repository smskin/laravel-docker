<?php

namespace App\Modules\Core;

use Illuminate\Queue\SerializesModels;

abstract class BaseRepresenter
{
    use SerializesModels;

    abstract public function toArray(): array;
}
