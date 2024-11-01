<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\DB;

abstract class Migration
{
    /**
     * @return string
     */
    public abstract function name() : string;
    /**
     * @param mixed $db
     *
     * @return void
     */
    public abstract function up($db) : void;
}
