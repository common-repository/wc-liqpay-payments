<?php

namespace Qodax\WcLiqPayPayments\Component;

use Qodax\WcLiqPayPayments\Model\Transaction;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View;

class QodaxLiqPayPaymentGateway extends \WC_Payment_Gateway
{
    private const META_STATUS = '_qxwlp_status';
    private const META_ACQUIRER_INCREMENTAL = '_qxwlp_acq_incremental';
    private const META_ACQUIRER_TRANSACTION_ID = '_qxwlp_acq_transaction_id';
    private const META_TRX_REFERENCE = '_qxwlp_trx_reference';

    private const SUPPORTED_CURRENCIES = [
        'EUR',
        'UAH',
        'USD',
    ];

    private string $publicKey;
    private string $privateKey;
    private bool $sandboxEnabled;
    private string $sandboxPublicKey;
    private string $sandboxPrivateKey;
    private string $resultUrl;
    private string $statusSuccess;
    private string $statusError;
    private string $lang;

    public function __construct()
    {
        $this->id = 'wc_liqpay_payments';
        $this->method_title = 'LiqPay by Qodax';
        $this->method_description = __('LiqPay payment gateway', 'wc-liqpay-payments');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->publicKey = $this->get_option('public_key');
        $this->privateKey = $this->get_option('private_key');
        $this->sandboxEnabled = $this->get_option('sandbox') === 'yes';
        $this->sandboxPublicKey = $this->get_option('sandbox_public_key');
        $this->sandboxPrivateKey = $this->get_option('sandbox_private_key');
        $this->resultUrl = $this->get_option('result_url');
        $this->statusSuccess = $this->get_option('status_success');
        $this->statusError = $this->get_option('status_error');
        $this->has_fields = false;
        $this->icon = $this->get_option('icon', QODAX_WC_LIQPAY_PLUGIN_URL . 'image/liqpay-icon.svg');
        $this->lang = $this->get_option('lang', 'uk');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_api_' . $this->id . '_process_callback', [$this, 'processCallback']);
        if ($this->get_option('processing_mode', 'redirect') === 'form') {
            add_action('woocommerce_receipt_wc_liqpay_payments', [$this, 'receiptPage']);
        }

        do_action(QODAX_WC_LIQPAY_PREFIX . 'gateway_loaded', $this);

        $this->init_form_fields();
        $this->init_settings();
    }

    // phpcs:ignore
    public function is_available(): bool
    {
        return parent::is_available() && in_array(get_woocommerce_currency(), self::SUPPORTED_CURRENCIES, true);
    }

    public function processCallback(): void
    {
        $processor = $this->createPaymentProcessor();

        try {
            $data = '';
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (isset($_POST['data'])) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing
                $data = sanitize_text_field($_POST['data']);
            }

            $signature = '';
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (isset($_POST['signature'])) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing
                $signature = sanitize_text_field($_POST['signature']);
            }

            $acquirerResult = $processor->processCallback($data, $signature);
            $order = wc_get_order($acquirerResult->getOrderId());
            if (!$order) {
                $this->internalPaymentError();
            } elseif ($order->get_meta(self::META_STATUS) !== 'pending') {
                return; // Already payed
            }

            if ($acquirerResult->hasSuccessStatus()) {
                $order->payment_complete($acquirerResult->getAcquirerTransactionId());
                $order->update_meta_data(
                    self::META_ACQUIRER_TRANSACTION_ID,
                    $acquirerResult->getAcquirerTransactionId()
                );
                $order->update_meta_data(self::META_TRX_REFERENCE, $acquirerResult->getTransactionReference());
                $order->update_status(
                    apply_filters(QODAX_WC_LIQPAY_PREFIX . 'success_status', $this->statusSuccess, $order)
                );
                wc_reduce_stock_levels($order->get_id());
                $resultMsg = 'Payment completed';
            } else {
                $order->update_status($this->statusError, 'Payment failed.');
                $resultMsg = 'Payment failure';
            }

            $order->update_meta_data(self::META_STATUS, $acquirerResult->getStatus());
            $order->add_order_note(
                sprintf(
                    "%s. Status - %s, Trx reference - %s",
                    $resultMsg,
                    $acquirerResult->getStatus(),
                    $acquirerResult->getTransactionReference()
                )
            );
            $order->save();
        } catch (\Exception $e) {
            $this->internalPaymentError();
        }
    }

    // phpcs:ignore
    public function admin_options()
    {
        $defaults = [
            'title' => '',
            'disabled' => false,
            'class' => '',
            'css' => '',
            'placeholder' => '',
            'type' => 'text',
            'desc_tip' => false,
            'description' => '',
            'custom_attributes' => [],
        ];

        $data['options'] = [];
        foreach ($this->form_fields as $key => $field) {
            $field_key = $this->get_field_key($key);
            $args = wp_parse_args($field, $defaults);
            $args['id'] = $field_key;
            $args['value'] = $this->get_option($key);
            $data['options'][$key] = $args;
        }

        View::render('gateway_options', $data);
    }

    // phpcs:ignore
    public function init_form_fields()
    {
        $statuses = wc_get_order_statuses();
        $formFields = [
            'enabled' => [
                'title' => __('Enable', 'wc-liqpay-payments'),
                'type' => 'checkbox',
                'label' => __('Enable', 'wc-liqpay-payments'),
                'default' => 'yes',
            ],
            'title' => [
                'title' => __('Title', 'wc-liqpay-payments'),
                'type' => 'text',
                'description' => __('Title that appears on the checkout page', 'wc-liqpay-payments'),
                'default' => 'LiqPay',
                'desc_tip' => true,
            ],
            'description' => [
                'title' => __('Description', 'wc-liqpay-payments'),
                'type' => 'textarea',
                'description' => __('Description that appears on the checkout page', 'wc-liqpay-payments'),
                'default' => __('Pay using LiqPay payment system', 'wc-liqpay-payments'),
                'desc_tip' => true,
            ],
            'public_key' => [
                'title' => __('Public key', 'wc-liqpay-payments'),
                'type' => 'text',
                'description' => __('LiqPay public key. Required parameter.', 'wc-liqpay-payments'),
                'desc_tip' => true,
            ],
            'private_key' => [
                'title' => __('Private key', 'wc-liqpay-payments'),
                'type' => 'password',
                'description' => __('LiqPay private key. Required parameter.', 'wc-liqpay-payments'),
                'desc_tip' => true,
            ],
            'sandbox' => [
                'title' => __('Enable sandbox mode', 'wc-liqpay-payments'),
                'type' => 'checkbox',
                'default' => 'no',
            ],
            'sandbox_public_key' => [
                'title' => __('Sandbox public key', 'wc-liqpay-payments'),
                'type' => 'text',
                'description' => __('Required if sandbox mode enabled.', 'wc-liqpay-payments'),
                'desc_tip' => true,
            ],
            'sandbox_private_key' => [
                'title' => __('Sandbox private key', 'wc-liqpay-payments'),
                'type' => 'password',
                'description' => __('Required if sandbox mode enabled.', 'wc-liqpay-payments'),
                'desc_tip' => true,
            ],
            'processing_mode' => [
                'title' => __('Processing mode', 'wc-liqpay-payments'),
                'type' => 'select',
                'options' => [
                    'form' => __('Form', 'wc-liqpay-payments'),
                    'redirect' => __('Redirect', 'wc-liqpay-payments'),
                ],
                'description' => '',
                'desc_tip' => false,
                'default' => 'redirect',
            ],
            'lang' => [
                'title' => __('Language', 'wc-liqpay-payments'),
                'type' => 'select',
                'options' => [
                    'uk' => __('Ukrainian', 'wc-liqpay-payments'),
                    'en' => __('English', 'wc-liqpay-payments'),
                    'auto' => __('Detect automatically', 'wc-liqpay-payments'),
                ],
                'description' => __(
                    'Gateway language interface. Automatic detection supports only for WPML and Polylang plugins.',
                    'wc-liqpay-payments'
                ),
                'desc_tip' => true,
                'default' => 'uk',
            ],
            'status_success' => [
                'title' => __('Success status', 'wc-liqpay-payments'),
                'type' => 'select',
                'options' => $statuses,
                'description' => '',
                'desc_tip' => false,
                'default' => 'wc-processing',
            ],
            'status_error' => [
                'title' => __('Error status', 'wc-liqpay-payments'),
                'type' => 'select',
                'options' => $statuses,
                'description' => '',
                'desc_tip' => false,
                'default' => 'wc-failed',
            ],
            'result_url' => [
                'title' => __('Result URL', 'wc-liqpay-payments'),
                'type' => 'text',
                'default' => '',
                'description' => __(
                    // phpcs:ignore
                    'URL where customer will be redirected after a successful transaction. Leave blank to use default url.',
                    'wc-liqpay-payments'
                ),
                'desc_tip' => true,
            ],
            'icon' => [
                'title' => __('Icon URL', 'wc-liqpay-payments'),
                'type' => 'text',
                'default' => QODAX_WC_LIQPAY_PLUGIN_URL . 'image/liqpay-icon.svg',
                'description' => '',
                'desc_tip' => false,
            ]
        ];

        /**
         * @internal For internal usage only
         * @since 1.0.1
         */
        $this->form_fields = apply_filters(QODAX_WC_LIQPAY_PREFIX . 'gateway_form_fields', $formFields, $this);
    }

    // phpcs:ignore
    function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        if (!$order) {
            throw new \LogicException("Order " . (int)$order_id . " not found");
        }

        $incremental = $order->get_meta(self::META_ACQUIRER_INCREMENTAL);
        if (!$incremental) {
            $incremental = 0;
        }
        $incremental++;
        $order->update_meta_data(self::META_ACQUIRER_INCREMENTAL, $incremental);
        $order->update_meta_data(self::META_STATUS, 'pending');
        $order->save();

        $resultUrl = $this->get_return_url($order);
        if (trim($this->resultUrl) !== '') {
            $resultUrl = trim($this->resultUrl) . "?wc_order_id=$order_id";
        }
        WC()->cart->empty_cart();

        if ($this->get_option('processing_mode', 'redirect') === 'form') {
            $redirect = add_query_arg(
                'order-pay',
                $order->get_id(),
                add_query_arg('key', $order->get_order_key(), get_permalink(wc_get_page_id('pay')))
            );
        } else {
            $paymentProcessor = $this->createPaymentProcessor();
            $redirect = $paymentProcessor->buildPaymentUrl(
                new Transaction(
                    $order,
                    $this->createTransactionReference($order),
                    (float)$order->get_total(''),
                    $order->get_currency(),
                    $this->getDescription($order),
                    $this->getLanguage(),
                    home_url('wc-api/wc_liqpay_payments_process_callback'),
                    $resultUrl
                )
            );
        }

        return [
            'result' => 'success',
            'redirect' => $redirect,
        ];
    }

    public function receiptPage(int $orderId): void
    {
        $order = wc_get_order($orderId);
        if (!$order) {
        ?>
            <ul class="woocommerce-error" role="alert">
			    <li>
                    <?php esc_html_e('Order not found', 'wc-liqpay-payments'); ?>
                </li>
	        </ul>
        <?php
            return;
        }
        $paymentProcessor = $this->createPaymentProcessor();
        $resultUrl = $this->get_return_url($order);
        if (trim($this->resultUrl) !== '') {
            $resultUrl = trim($this->resultUrl) . "?wc_order_id=$orderId";
        }

        $paymentProcessor->buildReceiptForm(
            new Transaction(
                $order,
                $this->createTransactionReference($order),
                (float)$order->get_total(''),
                $order->get_currency(),
                $this->getDescription($order),
                $this->getLanguage(),
                home_url('wc-api/wc_liqpay_payments_process_callback'),
                $resultUrl
            )
        );
    }

    /**
     * @todo: method visibility should be refactored
     */
    public function createPaymentProcessor(): LiqPayPaymentProcessor
    {
        return new LiqPayPaymentProcessor(
            $this->sandboxEnabled ? $this->sandboxPublicKey : $this->publicKey,
            $this->sandboxEnabled ? $this->sandboxPrivateKey : $this->privateKey
        );
    }

    /**
     * @todo: method visibility should be refactored
     */
    public function createTransactionReference(\WC_Order $order): string
    {
        return sprintf('PY%06dX%02d', $order->get_id(), (int)$order->get_meta(self::META_ACQUIRER_INCREMENTAL));
    }

    /**
     * @todo: method visibility should be refactored
     */
    public function getLanguage(): string
    {
        if ($this->lang === 'auto') {
            $defaultLang = 'uk';
            $lang = $defaultLang;

            if (function_exists('wpml_get_current_language')) {
                $lang = wpml_get_current_language();
            } elseif (function_exists('pll_current_language')) {
                $lang = pll_current_language();
            }

            if (in_array($lang, ['uk', 'en'], true)) {
                return $lang;
            }

            return $defaultLang;
        } else {
            return $this->lang;
        }
    }

    private function internalPaymentError(): void
    {
        wp_die('Unable to process payment response');
    }

    private function getDescription(\WC_Order $order): string
    {
        return __('Payment for order #', 'wc-liqpay-payments') . $order->get_id();
    }
}
