<?php

namespace App\Http\Services;

use App\Exceptions\BadRatingServiceResponseException;
use App\Http\Services\GenericInterfaces\ExchangeServiceInterface;
use App\Http\Services\Responses\CurrencyExchangeResponse;
use Illuminate\Support\Facades\Http;

class ExchangeService implements ExchangeServiceInterface
{
    private $serviceUrl;
    private $timeout;

    public function __construct(string $serviceUrl, int $timeout = 30)
    {
        $this->serviceUrl = $serviceUrl;
        $this->timeout = $timeout;
    }

    /**
     * Get all available rates from external service
     *
     * @param string $sourceCurrency
     * @return array
     * @throws BadRatingServiceResponseException
     */
    private function getAllRates(string $sourceCurrency = ''): array
    {
        $response = Http::timeout($this->timeout)->get($this->serviceUrl, ['base' => $sourceCurrency]);

        if ($response->failed() || $response->header('Content-Type') !== 'application/json') {
            throw new BadRatingServiceResponseException('Invalid response from external service');
        }

        $resultArray = $response->json();

        if (array_key_exists('rates', $resultArray)) {
            return $resultArray['rates'];
        }

        return [];
    }

    /**
     * Get specified rate
     *
     * @param string $sourceCurrency
     * @param string $destinationCurrency
     * @return float
     * @throws BadRatingServiceResponseException
     */
    private function getRate(string $sourceCurrency, string $destinationCurrency): float
    {
        return $this->getAllRates($sourceCurrency)[$destinationCurrency];
    }

    /**
     * Exchange money to specified currency
     *
     * @param float $amount
     * @param string $sourceCurrency
     * @param string $destinationCurrency
     * @return CurrencyExchangeResponse
     * @throws BadRatingServiceResponseException
     */
    public function exchangeCurrency(float $amount, string $sourceCurrency, string $destinationCurrency): CurrencyExchangeResponse
    {
        $rate = $this->getRate($sourceCurrency, $destinationCurrency);
        return new CurrencyExchangeResponse($amount * $rate, $destinationCurrency);
    }

    /**
     * Get all available currencies
     *
     * @return array
     * @throws BadRatingServiceResponseException
     */
    public function getAvailableCurrencies(): array
    {
        return array_merge(array_keys($this->getAllRates()), ['EUR']);
    }
}
