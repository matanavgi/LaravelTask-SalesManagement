<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sale
 * @property string $product_name
 * @property string $currency
 * @property double $sale_price
 * @property string $payme_sale_code
 * @property string $sale_url
 */
class Sale extends Model
{
    use HasFactory;

    protected $fillable = ["payme_sale_code", "sale_url", "sale_price", "currency", "product_name"];

}
