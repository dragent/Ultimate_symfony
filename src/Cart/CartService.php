<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    /**
     * 
     */
    protected $session;

    /**
     * 
     */
    protected $product_repository;

    /**
     * 
     */
    public function  __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->product_repository = $productRepository;
    }

    /**
     * 
     */
    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    /**
     * 
     */
    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    /**
     * 
     */
    public function add($id)
    {
        $cart = $this->getCart();
        if (array_key_exists($id, $cart)) {

            $cart[$id] = 0;
        }
        $cart[$id]++;
        $this->saveCart($cart);
    }

    /**
     * 
     */
    public function getItemNumber(): int
    {
        $total_qty = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->product_repository->find($id);
            if (!$product) {
                continue;
            }
            $total_qty += $qty;
        }
        return $total_qty;
    }

    /**
     * 
     */
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getCart()  as $id => $qty) {
            $product = $this->product_repository->find($id);
            if (!$product) {
                continue;
            }
            $total += $qty * $product->getPrice() / 100;
        }
        return $total;
    }

    /**
     * 
     */
    public function getDetailedCartItems(): array
    {
        $detailed_cart = [];
        foreach ($this->getCart()  as $id => $qty) {
            $product = $this->product_repository->find($id);
            if (!$product) {
                continue;
            }
            $detailed_cart[] = new CartItem($product, $qty);
        }
        return $detailed_cart;
    }

    /**
     * 
     */
    public function remove($id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart))
            return;
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    /**
     * 
     */
    public function decrement($id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart))
            return;
        if ($cart[$id] === 1)
            return $this->remove($id);
        $cart[$id]--;
        $this->saveCart($cart);
    }
}
