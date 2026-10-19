<?php

require_once __DIR__ . '/../classes/CartClass.php';

/**
 * CartController
 *
 * Connects action files and views to the CartClass model.
 * Does not contain SQL queries, echo HTML, or perform header redirects directly.
 */
class CartController
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartClass();
    }

    // ==========================================
    // TASK 11 METHODS
    // ==========================================

    /**
     * Check if a product is in the cart.
     *
     * @param  int    $productId
     * @param  string $ip
     * @return bool
     */
    public function isInCart($productId, $ip)
    {
        return $this->cartModel->isInCart($productId, $ip);
    }

    /**
     * Add a product to the cart.
     *
     * @param  int      $productId
     * @param  int      $qty
     * @param  string   $ip
     * @param  int|null $customerId
     * @return bool
     */
    public function addToCart($productId, $qty, $ip, $customerId = null)
    {
        return $this->cartModel->addToCart($productId, $qty, $ip, $customerId);
    }

    /**
     * Fetch all items in the cart.
     *
     * @param  string $ip
     * @return array
     */
    public function getCartItems($ip)
    {
        return $this->cartModel->getCartItems($ip);
    }

    /**
     * Get the count of cart items.
     *
     * @param  string $ip
     * @return int
     */
    public function getCartCount($ip)
    {
        return $this->cartModel->getCartCount($ip);
    }

    /**
     * Get the monetary total of all cart items.
     *
     * @param  string $ip
     * @return float
     */
    public function getCartTotal($ip)
    {
        return $this->cartModel->getCartTotal($ip);
    }

    // ==========================================
    // TASK 12 METHODS
    // ==========================================

    /**
     * Remove an item from the cart.
     *
     * @param  int    $productId
     * @param  string $ip
     * @return bool
     */
    public function removeFromCart($productId, $ip)
    {
        return $this->cartModel->removeFromCart($productId, $ip);
    }

    /**
     * Update quantity of an item in the cart.
     *
     * @param  int    $productId
     * @param  int    $newQty
     * @param  string $ip
     * @return bool
     */
    public function updateQty($productId, $newQty, $ip)
    {
        return $this->cartModel->updateQty($productId, $newQty, $ip);
    }
}
