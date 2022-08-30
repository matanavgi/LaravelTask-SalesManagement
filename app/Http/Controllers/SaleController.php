<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SaleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Sale $sale)
    {
        // Use the query structure to order by creation date.
        $salesData = $sale::select('*')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sales.index', compact('salesData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Display the create.blade view
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {

        if (!$request->has("price") || !$request->has("currency") || !$request->has("productName")) {
            return response('Missing parameters');
        }

        $productName = $request->get("productName");
        $price = $request->get("price");
        $currency = $request->get("currency");

        /*
         * Call the PayMe API for seller information
         * */
        $apiRes = Http::post(config("sellerData.generate_sale_api"), [
            "seller_payme_id" => config("sellerData.seller_payme_id"), // Use this static ID
            "sale_price"      => $price,  // From input. Price is in cents
            "currency"        => $currency, // From input
            "product_name"    => $productName, // From input
            "installments"    => config("sellerData.installments"), // Constant value
            "language"        => config("sellerData.language") // Constant value
        ]);

        $statusCode = $apiRes->json("status_code");

        // 1 => operation failed
        if ($statusCode == 1) {
            $errMessage = $apiRes->json("status_error_details");
            return view('sales.create', compact('errMessage'));
        }
        // 0 => operation succeed
        else {
            /*
             * Post new sale to DB
             * */
            $sale = new Sale();
            $sale->payme_sale_code = $apiRes->json("payme_sale_code");
            $sale->sale_url = $apiRes->json("sale_url");
            $sale->sale_price = $price;
            $sale->currency = $currency;
            $sale->product_name = $productName;

            $sale->save();

            /*
             * Show the payment form by the IFRAME
             * */
            $saleUrl = $apiRes->json("sale_url");

            return view('sales.create', compact('saleUrl'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $paymeSaleCode)
    {

        if (!$request->has("sale_price") || !$request->has("currency") || !$request->has("product_name")) {
            return response('Missing parameters');
        }

        Sale::where("payme_sale_code", $paymeSaleCode)
            ->update([
                "sale_price"   => $request->get("sale_price"),
                "currency"     => $request->get("currency"),
                "product_name" => $request->get("product_name")
            ]);

        return response(response()->json($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $paymeSaleCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $paymeSaleCode)
    {
        Sale::where('payme_sale_code', $paymeSaleCode)->delete();
    }
}
