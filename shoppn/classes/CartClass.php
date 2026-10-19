<?php

require_once __DIR__ . '/../core/db_class.php';

/**
 * CartClass (Model)
 *
 * Handles all database operations for the shopping cart.
 * All queries use prepared statements to prevent SQL injection.
 * No HTML or session output is generated in this class.
 */
class CartClass extends Database
{
    // ==========================================
    // TASK 11: ADD TO CART & CART SUMMARY
    // ==========================================

    /**
     * Check if a product is already in the cart for a given IP address.
     *
     * @param  int    $product_id
     * @param  string $ip
     * @return bool
     */
    public function isInCart($product_id, $ip)
    {
        $product_id = (int) $product_id;
        $ip = trim((string) $ip);

        $stmt = $this->conn->prepare('SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?');
        if (!$stmt) {
            error_log('CartClass::isInCart prepare failed: ' . $this->conn->error);
            return false;
        }

        $stmt->bind_param('is', $product_id, $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = ($result && $result->num_rows > 0);
        $stmt->close();

        return $exists;
    }

    /**
     * Add a product to the cart.
     * Supports both guest users (customer_id = null) and logged-in users.
     *
     * @param  int      $product_id
     * @param  int      $qty
     * @param  string   $ip
     * @param  int|null $customer_id
     * @return bool
     */
    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        $product_id = (int) $product_id;
        $qty = max(1, (int) $qty);
        $ip = trim((string) $ip);

        if ($customer_id !== null && (int) $customer_id > 0) {
            $cid = (int) $customer_id;
            $stmt = $this->conn->prepare('INSERT INTO cart (p_id, ip_add, qty, c_id) VALUES (?, ?, ?, ?)');
            if (!$stmt) {
                error_log('CartClass::addToCart prepare failed: ' . $this->conn->error);
                return false;
            }
            $stmt->bind_param('isii', $product_id, $ip, $qty, $cid);
        } else {
            $stmt = $this->conn->prepare('INSERT INTO cart (p_id, ip_add, qty) VALUES (?, ?, ?)');
            if (!$stmt) {
                error_log('CartClass::addToCart prepare failed: ' . $this->conn->error);
                return false;
            }
            $stmt->bind_param('isi', $product_id, $ip, $qty);
        }

        $result = $stmt->execute();
        if (!$result) {
            error_log('CartClass::addToCart execute failed: ' . $stmt->error);
        }
        $stmt->close();

        return $result;
    }

    /**
     * Retrieve all cart items for a given IP with joined product information.
     *
     * @param  string $ip
     * @return array
     */
    public function getCartItems($ip)
    {
        $ip = trim((string) $ip);

        $stmt = $this->conn->prepare(
            'SELECT cart.*, products.product_title, products.product_price, products.product_image
             FROM cart
             JOIN products ON cart.p_id = products.product_id
             WHERE cart.ip_add = ?'
        );

        if (!$stmt) {
            error_log('CartClass::getCartItems prepare failed: ' . $this->conn->error);
            return [];
        }

        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $items ?: [];
    }

    /**
     * Get the count of distinct product entries in the cart for a given IP.
     *
     * @param  string $ip
     * @return int
     */
    public function getCartCount($ip)
    {
        $ip = trim((string) $ip);

        $stmt = $this->conn->prepare('SELECT COUNT(*) AS item_count FROM cart WHERE ip_add = ?');
        if (!$stmt) {
            error_log('CartClass::getCartCount prepare failed: ' . $this->conn->error);
            return 0;
        }

        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return $row ? (int) $row['item_count'] : 0;
    }

    /**
     * Calculate total price for all items in the cart for a given IP.
     *
     * @param  string $ip
     * @return float
     */
    public function getCartTotal($ip)
    {
        $items = $this->getCartItems($ip);
        $total = 0.0;

        foreach ($items as $item) {
            $qty = isset($item['qty']) ? (int) $item['qty'] : 1;
            $price = isset($item['product_price']) ? (float) $item['product_price'] : 0.0;
            $total += ($qty * $price);
        }

        return (float) $total;
    }

    // ==========================================
    // TASK 12: CART MANAGEMENT (Remove & Update)
    // ==========================================

    /**
     * Remove an item from the cart by product ID and IP.
     *
     * @param  int    $product_id
     * @param  string $ip
     * @return bool
     */
    public function removeFromCart($product_id, $ip)
    {
        $product_id = (int) $product_id;
        $ip = trim((string) $ip);

        if ($product_id <= 0) {
            return false;
        }

        $stmt = $this->conn->prepare('DELETE FROM cart WHERE p_id = ? AND ip_add = ?');
        if (!$stmt) {
            error_log('CartClass::removeFromCart prepare failed: ' . $this->conn->error);
            return false;
        }

        $stmt->bind_param('is', $product_id, $ip);
        $result = $stmt->execute();
        if (!$result) {
            error_log('CartClass::removeFromCart execute failed: ' . $stmt->error);
        }
        $stmt->close();

        return $result;
    }

    /**
     * Update the quantity of a cart item.
     * Validates that new_qty is at least 1.
     *
     * @param  int    $product_id
     * @param  int    $new_qty
     * @param  string $ip
     * @return bool
     */
    public function updateQty($product_id, $new_qty, $ip)
    {
        $product_id = (int) $product_id;
        $new_qty = (int) $new_qty;
        $ip = trim((string) $ip);

        // Validation required by lab sheet: new_qty >= 1
        if ($new_qty < 1 || $product_id <= 0) {
            return false;
        }

        $stmt = $this->conn->prepare('UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?');
        if (!$stmt) {
            error_log('CartClass::updateQty prepare failed: ' . $this->conn->error);
            return false;
        }

        $stmt->bind_param('iis', $new_qty, $product_id, $ip);
        $result = $stmt->execute();
        if (!$result) {
            error_log('CartClass::updateQty execute failed: ' . $stmt->error);
        }
        $stmt->close();

        return $result;
    }
}
