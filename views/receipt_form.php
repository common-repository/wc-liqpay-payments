<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>
<?php do_action('qxwlp_before_receipt_form', $order); ?>
<p><?php esc_html_e('Thank you for your order! Click the button below to pay.', 'wc-liqpay-payments'); ?></p>
<form method="POST" action="<?php echo esc_attr($apiUrl); ?>" accept-charset="utf-8">
    <input type="hidden" name="data" value="<?php echo esc_attr($data); ?>"/>
    <input type="hidden" name="signature" value="<?php echo esc_attr($signature); ?>"/>
    <input type="image" style="width: 150px;" src="<?php echo esc_attr($btnUrl); ?>"/>
</form>
<?php do_action('qxwlp_after_receipt_form', $order); ?>
