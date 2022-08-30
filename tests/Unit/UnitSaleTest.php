<?php

namespace Tests\Unit;

use App\Models\Sale;
use Database\Seeders\SalesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UnitSaleTest extends TestCase
{
    // use RefreshDatabase;
    //protected $seeder = SalesTableSeeder::class;

    /**
     * Test getting response from the GET sales route.
     */
    public function test_sales_data_uri()
    {
        $response = $this->get('/sales');
        $response->assertOk();
    }

    /**
     * Test getting response from the GET sales creation route.
     */
    public function test_sales_create_uri()
    {
        $response = $this->get('/sales/create');
        $response->assertOk();
    }

    /**
     * Test getting response from the POST sales creation route.
     */
    public function test_sales_post_uri()
    {
        $response = $this->post('/sales');
        $response->assertOk();
    }

    /**
     * Test that got status OK from the POST PayMe API.
     */
    public function test_sales_payme_api_full_body()
    {
        $apiResOk = Http::post(config("sellerData.generate_sale_api"), [
            "seller_payme_id" => config("sellerData.seller_payme_id"), // Use this static ID
            "sale_price"      => 123,  // From input. Price is in cents
            "currency"        => 'ILS', // From input
            "product_name"    => 'Pants', // From input
            "installments"    => config("sellerData.installments"), // Constant value
            "language"        => config("sellerData.language") // Constant value
        ]);

        $this->assertTrue($apiResOk->json("status_code") == 0);

    }

    /**
     * Test that got status Failed from the POST PayMe API.
     */
    public function test_sales_payme_api_missing_input()
    {
        $apiResFail = Http::post(config("sellerData.generate_sale_api"), [
            "seller_payme_id" => config("sellerData.seller_payme_id"),
            "sale_price"      => "",  // Missing input
            "currency"        => 'ILS',
            "product_name"    => 'Pants',
            "installments"    => config("sellerData.installments"),
            "language"        => config("sellerData.language")
        ]);

        $this->assertTrue($apiResFail->json("status_code") == 1);

    }

    /**
     * Test that Sales table populate with the seed data
     */
    public function test_sales_table_data()
    {
        $this->seed(SalesTableSeeder::class);
        $this->assertGreaterThanOrEqual(3,Sale::all()->count());
    }


}
