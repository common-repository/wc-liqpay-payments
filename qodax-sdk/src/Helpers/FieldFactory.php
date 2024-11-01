<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Helpers;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Plugin;
class FieldFactory
{
    private string $prefix;
    public function __construct()
    {
        $this->prefix = Plugin::config()->get('ui_prefix');
    }
    public function textbox(string $name, string $label = '', string $value = '', array $options = []) : void
    {
        $id = $name;
        if (isset($options['id'])) {
            $id = $options['id'];
        }
        ?>
        <div class="<?php 
        echo esc_attr($this->prefixedClass('form-group'));
        ?>">
            <?php 
        if ($label !== '') {
            ?>
                <label for="<?php 
            echo esc_attr($id);
            ?>"><?php 
            echo esc_html($label);
            ?></label>
            <?php 
        }
        ?>
            <input type="text"
                   id="<?php 
        echo esc_attr($id);
        ?>"
                   name="<?php 
        echo esc_attr($name);
        ?>"
                   class="<?php 
        echo esc_attr($this->prefixedClass('form-control'));
        ?>"
                   value="<?php 
        echo esc_attr($value);
        ?>">
        </div>
        <?php 
    }
    private function prefixedClass(string $class) : string
    {
        return $this->prefix . $class;
    }
}
