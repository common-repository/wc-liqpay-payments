<?php

namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers\FieldFactory;
final class View
{
    private static ?\Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View $instance = null;
    /**
     * @var string[]
     */
    private array $patches = [];
    public static function instance() : \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View
    {
        if (self::$instance === null) {
            self::$instance = new \Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View();
        }
        return self::$instance;
    }
    public static function render(string $view, array $data = []) : void
    {
        self::instance()->realRender($view, $data);
    }
    public function addBasePath(string $path, string $namespace = '_') : void
    {
        $this->patches[$namespace] = $path;
    }
    public function realRender(string $view, array $data = []) : void
    {
        [$namespace, $fileName] = $this->parseViewName($view);
        if (\count($this->patches) === 0) {
            return;
        } elseif (!isset($this->patches[$namespace])) {
            return;
        }
        $filePath = $this->patches[$namespace] . "/{$fileName}.php";
        if (!\file_exists($filePath)) {
            return;
        }
        $data['fieldFactory'] = new FieldFactory();
        \ob_start();
        \extract($data);
        include $filePath;
        \ob_end_flush();
    }
    /**
     * @param string $view
     * @return string[]
     */
    private function parseViewName(string $view) : array
    {
        $namespace = '_';
        $fileName = $view;
        $parts = \explode('::', $view);
        if (\count($parts) === 2) {
            $namespace = $parts[0];
            $fileName = $parts[1];
        }
        return [$namespace, $fileName];
    }
}
