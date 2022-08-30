<?php

namespace Tests\Feature;

use App\Models\Sale;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FeatureSaleTest extends TestCase
{

    /**
     * Test for create and delete new sale
     */
    public function test_sale_create_and_delete_db()
    {
        /*
         * Call the API for additional seller data
         * */
        $apiRes = Http::post(config("sellerData.generate_sale_api"), [
            "seller_payme_id" => config("sellerData.seller_payme_id"), // Use this static ID
            "sale_price" => 12345,  // From input. Price is in cents
            "currency" => "ILS", // From input
            "product_name" => "Shirt", // From input
            "installments" => config("sellerData.installments"), // Constant value
            "language" => config("sellerData.language") // Constant value
        ]);
        $saleUrl = $apiRes->json("sale_url");

        // Assert that the API call succeed.
        $this->assertTrue($apiRes->json("status_code") == 0);

        $response = $this->followingRedirects()
            ->get('/sales/create', ['saleUrl' => $saleUrl])
            ->assertOk();

        /*
         * Post new sale to DB
         * */
        $sale = new Sale();
        $sale->payme_sale_code = $apiRes->json("payme_sale_code");
        $sale->sale_url = $apiRes->json("sale_url");
        $sale->sale_price = 12345;
        $sale->currency = "ILS";
        $sale->product_name = "Shirt";

        $sale->save();

        // Assert that sale object inserted to DB
        $this->assertDatabaseHas("sales", [
            "payme_sale_code" => $apiRes->json("payme_sale_code")
        ]);

        // Delete last inserted sale
        $this->delete("/sales/".$apiRes->json("payme_sale_code"));

        // Assert that sale record deleted
        $this->assertDatabaseMissing("sales", [
            "payme_sale_code" => $apiRes->json("payme_sale_code")
        ]);
    }
}
