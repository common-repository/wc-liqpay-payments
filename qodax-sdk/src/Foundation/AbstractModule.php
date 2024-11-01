<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\Routing\Router;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\Container;
abstract class AbstractModule
{
    protected Container $container;
    protected Router $router;
    public function setContainer(Container $container) : void
    {
        $this->container = $container;
    }
    public function setRouter(Router $router) : void
    {
        $this->router = $router;
    }
    public abstract function boot() : void;
}
