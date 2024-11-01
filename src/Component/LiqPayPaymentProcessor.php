<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Component;

use Qodax\WcLiqPayPayments\Exception\ProcessingException;
use Qodax\WcLiqPayPayments\Model\AcquirerResult;
use Qodax\WcLiqPayPayments\Model\Transaction;
use Qodax\WcLiqPayPayments\Util\LiqPayEncryptor;
use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Foundation\View;

class LiqPayPaymentProcessor
{
    private const ACTION_PAY = 'pay';
    private const API_URL = 'https://www.liqpay.ua/api/3/checkout';
    private const VERSION = 3;

    private string $publicKey;
    private LiqPayEncryptor $encryptor;

    public function __construct(string $publicKey, string $privateKey)
    {
        $this->publicKey = $publicKey;
        $this->encryptor = new LiqPayEncryptor($privateKey);
    }

    public function buildPaymentUrl(Transaction $transaction): string
    {
        $data = $this->buildPaymentData($transaction);

        return sprintf(
            '%s?data=%s&signature=%s',
            self::API_URL,
            $this->encryptor->encodeData($data),
            $this->encryptor->generateSignature($data)
        );
    }

    public function buildReceiptForm(Transaction $transaction): void
    {
        $data = $this->buildPaymentData($transaction);

        View::render('receipt_form', [
            'apiUrl' => self::API_URL,
            'data' => $this->encryptor->encodeData($data),
            'signature' => $this->encryptor->generateSignature($data),
            'btnUrl' => QODAX_WC_LIQPAY_PLUGIN_URL . 'image/pay_button.png',
            'order' => $transaction->getOrder(),
        ]);
    }

    /**
     * @throws Qodax\WcLiqPayPayments\Exception\ProcessingException
     */
    public function processCallback(string $encodedData, string $signature): AcquirerResult
    {
        if ($encodedData === '' || $signature === '') {
            throw new ProcessingException('Request has empty data or signature');
        }

        $validSignature = $this->encryptor->generateSignatureFromRaw($encodedData);
        if ($validSignature !== $signature) {
            throw new ProcessingException('Signatures are not equals');
        }

        $data = $this->encryptor->decodeData($encodedData);

        return new AcquirerResult(
            $data['order_id'], // Internal reference
            $data['liqpay_order_id'],
            $data['status'],
            (int)$data['info'], // WC order id
            (float)$data['amount'],
            $data['currency'],
            $data['ip'] ?? '',
            $data['err_code'] ?? '',
            $data['err_description'] ?? '',
            $data['paytype'] ?? ''
        );
    }

    private function buildPaymentData(Transaction $transaction): array
    {
        $data = [
            'version' => self::VERSION,
            'public_key' => $this->publicKey,
            'order_id' => $transaction->getUniqueId(),
            'action' => self::ACTION_PAY,
            'description' => $transaction->getDescription(),
            'language' => $transaction->getLanguage(),
            'amount' => $transaction->getAmount(),
            'currency' => $transaction->getCurrency(),
            'result_url' => $transaction->getResultUrl(),
            'server_url' => $transaction->getCallbackUrl(),
            'info' => $transaction->getOrder()->get_id(),
        ];

        return apply_filters('qxwlp_gateway_payment_data', $data, $transaction);
    }
}
