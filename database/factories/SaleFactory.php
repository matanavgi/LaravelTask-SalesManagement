<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $products = ["Shirt", "Pants", "Belt", "Shoes", "Glasses"];
        $currencies = ["ILS", "USD", "EUR"];
        $saleUrlPrefix = "https://sandbox.payme.io/sale/generate/FAKER-TESTING-SALE1661-";

        return [
            "payme_sale_code" => $this->faker->numerify("112####"),
            "sale_price" => rand(100, 999999),
            "currency" => $currencies[array_rand($currencies)],
            "product_name" =>  $products[array_rand($products)],
            "sale_url" => $saleUrlPrefix . $this->faker->regexify("[A-Z0-9]{8}")
        ];
    }
}
