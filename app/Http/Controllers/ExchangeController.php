<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestFormatException;
use App\Http\Services\GenericInterfaces\ExchangeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExchangeController extends Controller
{
    private $exchangeService;
    private $numberDecimals;
    private $decPoint;
    private $thousandsSep;

    public function __construct(ExchangeServiceInterface $exchangeService)
    {
        $this->exchangeService = $exchangeService;

        $this->numberDecimals = env('NUMBER_DECIMALS', 2);
        $this->decPoint = env('DEC_POINT', '.');
        $this->thousandsSep = env('THOUSANDS_SEP', '.');
    }

    /**
     * Exchange GET endpoint
     *
     * @param Request $request
     * GET param sourceCurrency: string = Source currency
     * GET param destinationCurrency: string = Destination currency
     * GET param amount: float = Amount to exchange
     * @return JsonResponse
     */
    public function exchange(Request $request): JsonResponse
    {
        $availableCurrencies = $this->exchangeService->getAvailableCurrencies();

        $validateMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute field must be string.',
            'numeric' => 'The :attribute field must be numeric.',
            'in' => 'The :attribute must be one of the following types: :values'
        ];

        $rules = [
            'sourceCurrency' => [
                Rule::in($availableCurrencies),
                'required',
                'string'
            ],
            'destinationCurrency' => [
                Rule::in($availableCurrencies),
                'required',
                'string'
            ],
            'amount' => [
                'required',
                'numeric'
            ]
        ];

        $validateResult = Validator::make($request->all(), $rules, $validateMessages)->errors()->all(); //validate request

        if (!empty($validateResult)) {
            throw new BadRequestFormatException($validateResult);
        }

        $result = $this->exchangeService->exchangeCurrency($request->get('amount'),
            $request->get('sourceCurrency'), $request->get('destinationCurrency'));

        return response()->json(['currency' => $result->getCurrency(),
            'amount' => number_format($result->getAmount(), $this->numberDecimals, $this->decPoint, $this->thousandsSep)
        ]);
    }

    /**
     * All available currencies in service GET endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrencies(Request $request): JsonResponse
    {
        return response()->json($this->exchangeService->getAvailableCurrencies());
    }
}
