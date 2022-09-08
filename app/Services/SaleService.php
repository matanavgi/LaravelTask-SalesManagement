<?php
/**
 * Created by PhpStorm.
 * User: matan
 * Date: 08/09/2022
 * Time: 11:37
 */

namespace App\Services;

use App\Models\Sale;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SaleService
{

    /**
     * @param string $product_name
     * @param string $currency
     * @param double $sale_price
     * @param string $payme_sale_code
     * @param string $sale_url
     * @return Sale
     */
    public function create(string $product_name, string $currency, float $sale_price, string $payme_sale_code, string $sale_url): Sale
    {
        $sale = new Sale();
        $sale->payme_sale_code = $payme_sale_code;
        $sale->sale_url = $sale_url;
        $sale->sale_price = $sale_price;
        $sale->currency = $currency;
        $sale->product_name = $product_name;

        return $sale;
    }

    /**
     * @param string $product_name
     * @param string $currency
     * @param float $sale_price
     * @return Response
     */
    public function getPayMeApiSellerData(string $product_name, string $currency, float $sale_price): Response
    {
        /*
         * Call the PayMe API for seller information
         * */
        return Http::post(config("seller_data.generate_sale_api"), [
            "seller_payme_id" => config("seller_data.seller_payme_id"), // Use this static ID
            "sale_price"      => $sale_price,  // From input. Price is in cents
            "currency"        => $currency, // From input
            "product_name"    => $product_name, // From input
            "installments"    => config("seller_data.installments"), // Constant value
            "language"        => config("seller_data.language") // Constant value
        ]);
    }

}
