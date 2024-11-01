<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }
?>

<div class="qxwlp-application">
    <div class="qxwlp-application__header">
        <div class="qxwlp-application__title">
            <?php esc_html_e('Settings', 'wc-liqpay-payments'); ?> - WC LiqPay Payments
        </div>
        <a href="https://kirillbdev.pro/docs/wc-liqpay-payments-base-setup/"
           target="_blank"
           class="qxwlp-btn qxwlp-btn--md qxwlp-btn--default">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M451.457,138.456L317.892,4.891C314.903,1.9,310.674,0,306.087,0H72.348c-9.22,0-16.696,7.475-16.696,16.696v478.609c0,9.22,7.475,16.696,16.696,16.696h367.304c9.22,0,16.696-7.475,16.696-16.696V150.261C456.348,146.02,454.668,141.666,451.457,138.456z M322.783,57.002l38.281,38.281l38.282,38.282h-76.563V57.002z M422.957,478.609H89.043V33.391h200.348v116.87c0,9.22,7.475,16.696,16.696,16.696h116.87V478.609z"/></g></g><g><g><path d="M372.87,244.87H139.13c-9.22,0-16.696,7.475-16.696,16.696c0,9.22,7.475,16.696,16.696,16.696H372.87c9.22,0,16.696-7.475,16.696-16.696C389.565,252.345,382.09,244.87,372.87,244.87z"/></g></g><g><g><path d="M372.87,311.652H139.13c-9.22,0-16.696,7.475-16.696,16.696s7.475,16.696,16.696,16.696H372.87c9.22,0,16.696-7.475,16.696-16.696S382.09,311.652,372.87,311.652z"/></g></g><g><g><path d="M272.696,378.435H139.13c-9.22,0-16.696,7.475-16.696,16.696s7.475,16.696,16.696,16.696h133.565c9.22,0,16.696-7.475,16.696-16.696S281.916,378.435,272.696,378.435z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
            <?php esc_html_e('Documentation', 'wc-liqpay-payments'); ?>
        </a>
    </div>
    <div class="qxwlp-application__content">

        <?php foreach ($options as $key => $option) { ?>
        <?php if ($option['type'] !== 'custom') { ?>
                <div class="qxwlp-form-group <?php echo $option['type'] === 'checkbox' ? 'qxwlp-form-group--horizontal' : ''; ?>">
                    <?php if ($option['type'] !== 'checkbox') { ?>
                        <label for="<?php echo esc_attr($option['id']); ?>"><?php echo wp_kses_post($option['title']); ?></label>
                    <?php } ?>
                    <?php if (in_array($option['type'], ['text', 'password'], true)) { ?>
                        <input type="<?php echo esc_attr($option['type']); ?>"
                               id="<?php echo esc_attr($option['id']); ?>"
                               name="<?php echo esc_attr($option['id']); ?>"
                               class="qxwlp-form-control"
                               value="<?php echo esc_attr($option['value']); ?>">
                    <?php } elseif ($option['type'] === 'textarea') { ?>
                        <textarea id="<?php echo esc_attr($option['id']); ?>"
                                  name="<?php echo esc_attr($option['id']); ?>"
                                  class="qxwlp-form-control"><?php echo esc_html($option['value']); ?></textarea>
                    <?php } elseif ($option['type'] === 'select') { ?>
                        <select id="<?php echo esc_attr($option['id']); ?>"
                                name="<?php echo esc_attr($option['id']); ?>"
                                class="qxwlp-form-control">
                            <?php foreach ($option['options'] as $value => $name) { ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php echo $value === $option['value'] ? 'selected' : ''; ?>><?php echo esc_html($name); ?></option>
                            <?php } ?>
                        </select>
                    <?php } elseif ($option['type'] === 'checkbox') { ?>
                        <label class="qxwlp-switcher">
                            <input type="checkbox"
                                   name="<?php echo esc_attr($option['id']); ?>"
                                   value="1"
                                   <?php echo $option['value'] === 'yes' ? 'checked' : ''; ?>
                            >
                            <span class="qxwlp-switcher__control"></span>
                            <span class="qxwlp-switcher__label"><?php echo wp_kses_post($option['title']); ?></span>
                        </label>
                    <?php } ?>
                    <?php if ($option['desc_tip'] === true && ! empty($option['description'])) { ?>
                        <div class="qxwlp-form-group__tooltip"><?php echo esc_html($option['description']); ?></div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <script>
                    window.<?php echo esc_js($option['id']); ?> = <?php echo wp_json_encode($option); ?>;
                </script>
                <div id="<?php echo esc_attr($option['id']); ?>"></div>
            <?php } ?>
        <?php } ?>

    </div>
</div>
