<?php

namespace Tests\Feature;

use App\Models\Sale;
use Tests\TestCase;

class FeatureSaleTest extends TestCase
{

    /**
     * Test sale creation
     */
    public function test_sale_create()
    {
        // Make Sale model without persisting it to the DB
        $sale = Sale::factory()->make();

        // Run the Store function from the Sales Controller
        $this->post("/sales", [
            "price"       => $sale->sale_price,
            "currency"    => $sale->currency,
            "productName" => $sale->product_name
        ])
            ->assertOk()
            ->assertViewIs("sales.create");
    }

    /**
     * Test sale creation with missing parameters.
     */
    public function test_sale_create_missing_params()
    {
        // Make Sale object without persisting it to the DB
        $sale = Sale::factory()->make();

        // Run the Store function in the Sales Controller
        $this->post("/sales", [
            "price"    => $sale->sale_price,
            "currency" => $sale->currency
        ])
            ->assertSessionHasErrors()
            ->assertStatus(302);
    }

    /**
     * Test sale creation with missing parameters.
     */
    public function test_sale_create_wrong_currency_value()
    {
        // Make Sale object without persisting it to the DB
        $sale = Sale::factory()->make();

        // Run the Store function in the Sales Controller
        $this->post("/sales", [
            "price"       => $sale->sale_price,
            "currency"    => "BHT",
            "productName" => $sale->product_name
        ])
            ->assertSessionHasErrors()
            ->assertStatus(302);

    }

    /**
     * Test sale model saved into DB
     */
    public function test_sale_db_creation_and_deletion()
    {
        // Create Sale model and save it into DB
        $sale = Sale::factory()->create();

        // Assert that sale object inserted to DB
        $this->assertDatabaseHas("sales", [
            "payme_sale_code" => $sale->payme_sale_code
        ]);

        // Delete last inserted sale
        $this->delete("/sales/" . $sale->payme_sale_code);

        // Assert that sale record deleted
        $this->assertDatabaseMissing("sales", [
            "payme_sale_code" => $sale->payme_sale_code
        ]);

    }

    /**
     * Test sale model been updated
     */
    public function test_sale_update()
    {
        // Get the last inserted Sale from DB.
        $sale = Sale::query()->get()->last();

        // Run the Store function in the Sales Controller
        $this->put("/sales/" . $sale->payme_sale_code, [
            "price"       => 1234,
            "currency"    => "EUR",
            "productName" => "Product Update"
        ])->assertOk();

        // Get the last inserted Sale from DB.
        $updatedSale = Sale::query()->get()->first();

        // Assert that last Sale model been updated
        $this->assertNotEquals($sale->product_name, $updatedSale->product_name);

    }

}
