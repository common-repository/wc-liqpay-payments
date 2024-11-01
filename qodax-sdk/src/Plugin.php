<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\Container;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\PluginConfig;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\Routing\Router;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\AbstractModule;
class Plugin
{
    protected static ?Plugin $instance = null;
    protected PluginConfig $config;
    protected Container $container;
    protected Router $router;
    private View $view;
    /**
     * @var string[]
     */
    private $modules;
    private function __construct(PluginConfig $config, array $dependencies, array $modules, string $viewPath)
    {
        $this->config = $config;
        $this->modules = $modules;
        // Register SDK modules
        $this->modules[] = \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules\Localization::class;
        $this->modules[] = \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules\FrontendLoader::class;
        // Register premium modules
        if (\class_exists(\Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules\Updater\Updater::class)) {
            $this->modules[] = \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Modules\Updater\Updater::class;
        }
        $this->view = View::instance();
        $this->view->addBasePath($this->config->get('framework_path') . '/views', 'fw');
        $this->view->addBasePath($viewPath);
        // Main plugin views
        // DI container
        $this->container = Container::instance($dependencies);
        $this->router = new Router();
    }
    public static function instance(PluginConfig $config, array $dependencies, array $modules, string $viewPath) : \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self($config, $dependencies, $modules, $viewPath);
        }
        return self::$instance;
    }
    public static function config() : PluginConfig
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Plugin instance has not been created');
        }
        return self::$instance->config;
    }
    public function init() : void
    {
        foreach ($this->modules as $module) {
            /** @var AbstractModule $moduleInstance */
            $moduleInstance = $this->container->make($module);
            if (!$moduleInstance instanceof AbstractModule) {
                // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                throw new \RuntimeException("Unable to boot module '{$module}'");
            }
            $moduleInstance->setContainer($this->container);
            $moduleInstance->setRouter($this->router);
            $moduleInstance->boot();
        }
    }
    public function getContainer() : Container
    {
        return $this->container;
    }
    public function getRouter() : Router
    {
        return $this->router;
    }
}
