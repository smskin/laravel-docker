<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Modules\Core;

abstract class BaseListener
{
    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var bool|null
     */
    public $afterCommit;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'listeners';
}
