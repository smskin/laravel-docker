<?php

use App\Modules\Core\Helpers\Helper;

if (! function_exists('hlp')) {
    function hlp(): Helper
    {
        return app(Helper::class);
    }
}
