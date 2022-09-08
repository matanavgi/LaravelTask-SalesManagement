<?php

namespace Tests\Unit;

use App\Models\Sale;
use Database\Seeders\SalesTableSeeder;
use Tests\TestCase;

class UnitSaleTest extends TestCase
{
    // use RefreshDatabase;
    // protected $seeder = SalesTableSeeder::class;

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
     * Test that Sales table populate with the seed data
     */
    public function test_sales_table_data()
    {
        $this->seed(SalesTableSeeder::class);
        $this->assertGreaterThanOrEqual(3, Sale::all()->count());
    }

}
