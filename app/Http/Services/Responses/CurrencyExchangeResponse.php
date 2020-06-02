<?php

namespace App\Http\Services\Responses;

class CurrencyExchangeResponse
{
    private $amount;
    private $currency;

    /**
     * CurrencyExchangeResponse constructor.
     * @param $amount
     * @param $currency
     */
    public function __construct(float $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

}
