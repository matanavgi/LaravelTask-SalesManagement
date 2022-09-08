<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Sale $sale
     * @return View
     */
    public function index(Sale $sale): View
    {
        Log::debug("[SaleController][index] Receive the sales data");
        // Use the query structure to order by creation date.
        $salesData = $sale::query()
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sales.index', compact('salesData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        Log::debug("[SaleController][create] Display the create.blade view");

        // Display the create.blade view
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSaleRequest $request
     * @return View
     */
    public function store(StoreSaleRequest $request): View
    {
        Log::debug("[SaleController][store] Add new sale to DB");

        $productName = $request->input("productName");
        $price = $request->input("price");
        $currency = $request->input("currency");

        $apiRes = (new SaleService)->getPayMeApiSellerData($productName, $currency, $price);

        $statusCode = $apiRes->json("status_code");

        // 1 => operation failed
        if ($statusCode == 1) {
            $errMessage = $apiRes->json("status_error_details");
            Log::error("[SaleController][store] Error on getting the PayMe API seller data. Err message: {$errMessage}");
            $viewCompactVarName = "errMessage";
        } // 0 => operation succeed
        else {

            $sale = (new SaleService)->create($productName, $currency, $price, $apiRes->json("payme_sale_code"), $apiRes->json("sale_url"));
            $sale->save();

            /*
             * Show the payment form by the IFRAME
             * */
            $saleUrl = $apiRes->json("sale_url");
            $viewCompactVarName = "saleUrl";
        }

        Log::debug("[SaleController][store] New sale added to DB");

        return view("sales.create", compact($viewCompactVarName));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreSaleRequest $request
     * @param string $paymeSaleCode
     * @return Response
     */
    public function update(StoreSaleRequest $request, string $paymeSaleCode): Response
    {
        Log::debug("[SaleController][update] Update existing sale by PayMe Sale Code: {$paymeSaleCode}");

        Sale::query()
            ->where("payme_sale_code", $paymeSaleCode)
            ->update([
                "sale_price"   => $request->input("price"),
                "currency"     => $request->input("currency"),
                "product_name" => $request->input("productName")
            ]);

        Log::debug("[SaleController][update] Sale been updated");

        return response(response()->json($request), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $paymeSaleCode
     * @return Response
     */
    public function destroy(string $paymeSaleCode): Response
    {
        Log::debug("[SaleController][destroy] Delete sale by PayMe Sale Code: {$paymeSaleCode}");

        Sale::query()
            ->where('payme_sale_code', $paymeSaleCode)->delete();

        Log::debug("[SaleController][update] Sale been deleted");

        return response("Sale deleted", Response::HTTP_NO_CONTENT);
    }
}
