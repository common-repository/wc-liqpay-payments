<?php

declare(strict_types=1);

namespace Qodax\WcLiqPayPayments\Model;

final class AcquirerResult
{
    private const SUCCESS_STATUSES = [
        'success',
        'wait_accept',
    ];

    private string $transactionReference;
    private string $acquirerTransactionId;
    private string $status;
    private int $orderId;
    private float $amount;
    private string $currency;
    private string $userIp;
    private string $errorCode;
    private string $errorDescription;
    private string $extraInfo1;

    public function __construct(
        string $transactionReference,
        string $acquirerTransactionId,
        string $status,
        int $orderId,
        float $amount,
        string $currency,
        string $userIp,
        string $errorCode,
        string $errorDescription,
        string $extraInfo1 = ''
    ) {
        $this->transactionReference = $transactionReference;
        $this->acquirerTransactionId = $acquirerTransactionId;
        $this->status = $status;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->userIp = $userIp;
        $this->errorCode = $errorCode;
        $this->errorDescription = $errorDescription;
        $this->extraInfo1 = $extraInfo1;
    }

    public function hasSuccessStatus(): bool
    {
        return in_array($this->status, self::SUCCESS_STATUSES, true);
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function getAcquirerTransactionId(): string
    {
        return $this->acquirerTransactionId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUserIp(): string
    {
        return $this->userIp;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getExtraInfo1(): string
    {
        return $this->extraInfo1;
    }
}
