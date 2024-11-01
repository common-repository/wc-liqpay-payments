<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation;

class PluginConfig
{
    private const REQUIRED_KEYS = ['plugin_id', 'plugin_name', 'plugin_version', 'global_prefix', 'framework_path', 'framework_js_obj'];
    private array $data = ['is_premium' => \false];
    public function __construct(array $data)
    {
        foreach (self::REQUIRED_KEYS as $key) {
            if (!isset($data[$key])) {
                // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
                throw new \InvalidArgumentException("Required key '{$key}' not set");
            }
        }
        $this->data = \array_replace($this->data, $data);
        foreach (['ajax_prefix', 'hook_prefix', 'option_prefix'] as $key) {
            if (!isset($this->data[$key])) {
                $this->data[$key] = $this->data['global_prefix'];
            }
        }
        if (!isset($this->data['ui_prefix'])) {
            $this->data['ui_prefix'] = \str_replace('_', '-', $this->data['global_prefix']);
        }
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
}
