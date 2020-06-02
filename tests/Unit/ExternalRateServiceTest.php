<?php

use App\Http\Services\ExchangeService;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class ExternalRateServiceTest extends TestCase
{
    protected const TEST_URL = 'https://testexchange.com';
    protected $responseBody;
    protected $exchangeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responseBody = file_get_contents(dirname(__FILE__) . '/rateServiceResponse.json');

        Http::fake([
            ExternalRateServiceTest::TEST_URL . '*' => Http::sequence()
                ->push($this->responseBody, 200, ['Content-Type' => 'application/json'])
        ]);

        $this->exchangeService = new ExchangeService(ExternalRateServiceTest::TEST_URL, 30);
    }

    public function testResponseFromExternalRateService()
    {
        $result = $this->exchangeService->exchangeCurrency(100.0, 'PLN', 'EUR');
        $this->assertEquals(22.47443533, $result->getAmount());
        $this->assertEquals('EUR', $result->getCurrency());
    }

    public function testAvailableCurrencies()
    {
        $ratesArray = array_keys(json_decode($this->responseBody, true)['rates']);
        array_push($ratesArray, 'EUR');
        $result = $this->exchangeService->getAvailableCurrencies();
        $this->assertEquals($ratesArray, $result);
    }
}
