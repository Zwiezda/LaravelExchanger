<?php

namespace App\Http\Services\GenericInterfaces;

use App\Http\Services\Responses\CurrencyExchangeResponse;

interface ExchangeServiceInterface
{
    function exchangeCurrency(float $amount, string $sourceCurrency, string $destinationCurrency): CurrencyExchangeResponse;

    function getAvailableCurrencies(): array;
}
