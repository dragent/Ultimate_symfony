<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem
{

    public $product;
    public $qty;

    public function __construct(Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    public function getTotal(): float
    {
        return $this->product->getPrice() / 100 * $this->qty;
    }
}