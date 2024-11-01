<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Model;

final class Transaction
{
    private const SUPPORTED_CURRENCIES = ['EUR', 'UAH', 'USD'];
    private const SUPPORTED_LANGUAGES = ['uk', 'en'];

    private \WC_Order $order;
    private string $uniqueId;
    private string $description;
    private float $amount;
    private string $currency;
    private string $callbackUrl;
    private string $resultUrl;

    public function __construct(
        \WC_Order $order,
        string $uniqueId,
        float $amount,
        string $currency,
        string $description,
        string $language,
        string $callbackUrl,
        string $resultUrl
    ) {
        if ($uniqueId === '') {
            throw new \InvalidArgumentException('Transaction unique id cannot be empty.');
        }
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }
        if (!in_array($currency, self::SUPPORTED_CURRENCIES, true)) {
            throw new \InvalidArgumentException(sprintf('Currency %s is not supported.', esc_html($currency)));
        }
        if (!in_array($language, self::SUPPORTED_LANGUAGES, true)) {
            throw new \InvalidArgumentException(sprintf('Language %s is not supported.', esc_html($language)));
        }
        if ($callbackUrl === '') {
            throw new \InvalidArgumentException('callbackUrl cannot be empty.');
        }
        if ($resultUrl === '') {
            throw new \InvalidArgumentException('resultUrl cannot be empty.');
        }

        $this->order = $order;
        $this->uniqueId = $uniqueId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->language = $language;
        $this->callbackUrl = $callbackUrl;
        $this->resultUrl = $resultUrl;
    }

    public function getOrder(): \WC_Order
    {
        return $this->order;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function getResultUrl(): string
    {
        return $this->resultUrl;
    }
}
