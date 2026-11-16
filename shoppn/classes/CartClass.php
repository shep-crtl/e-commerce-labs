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

    // ==========================================
    // TASK 13: PAYMENT & ORDER PROCESSING
    // ==========================================

    /**
     * Check if a payment reference has already been processed to prevent duplicates.
     *
     * @param  string $reference
     * @return bool
     */
    public function isReferenceProcessed($reference)
    {
        $reference = trim((string) $reference);
        if ($reference === '') {
            return false;
        }

        // Required schema migration: payment_ref must be UNIQUE.
        $stmt = $this->conn->prepare('SELECT pay_id FROM payment WHERE payment_ref = ? LIMIT 1');
        if (!$stmt) {
            error_log('CartClass::isReferenceProcessed requires payment.payment_ref: ' . $this->conn->error);
            return false;
        }
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $res = $stmt->get_result();
        $found = ($res && $res->num_rows > 0);
        $stmt->close();
        return $found;
    }

    /**
     * Process payment and record order inside a database transaction.
     *
     * 1. Generates 6-digit invoice number.
     * 2. Inserts into orders table.
     * 3. Copies purchased cart items into orderdetails table.
     * 4. Inserts payment record with verified reference.
     * 5. Deletes items from cart.
     * 6. Commits transaction and returns new order_id.
     *
     * @param  int    $customer_id
     * @param  string $ip
     * @param  string $reference
     * @param  float  $amount
     * @param  string $currency
     * @return int|false Returns new order_id on success, false on failure.
     */
    public function processPayment($customer_id, $ip, $reference, $amount, $currency = 'GHS')
    {
        $customer_id = (int) $customer_id;
        $ip = trim((string) $ip);
        $reference = trim((string) $reference);
        $amount = (float) $amount;

        if ($customer_id <= 0 || $amount <= 0 || $reference === '') {
            error_log('CartClass::processPayment: Invalid input parameters.');
            return false;
        }

        // Prevent duplicate processing of the same payment reference
        if ($this->isReferenceProcessed($reference)) {
            error_log('CartClass::processPayment: Reference ' . $reference . ' already processed.');
            return false;
        }

        // Retrieve current cart items
        $cartItems = $this->getCartItems($ip);
        if (empty($cartItems)) {
            error_log('CartClass::processPayment: Cart is empty for IP ' . $ip);
            return false;
        }

        // Start Database Transaction
        $this->conn->begin_transaction();

        try {
            // Lock cart rows and revalidate the amount before recording the order.
            $lock = $this->conn->prepare('SELECT c.p_id, c.qty, p.product_price FROM cart c JOIN products p ON p.product_id = c.p_id WHERE c.ip_add = ? FOR UPDATE');
            if (!$lock) { throw new Exception('Could not lock cart: ' . $this->conn->error); }
            $lock->bind_param('s', $ip);
            if (!$lock->execute()) { throw new Exception('Could not read locked cart.'); }
            $lockedItems = $lock->get_result()->fetch_all(MYSQLI_ASSOC);
            $lock->close();
            $lockedTotal = 0;
            foreach ($lockedItems as $item) { $lockedTotal += ((int)$item['qty']) * ((float)$item['product_price']); }
            if (count($lockedItems) !== count($cartItems) || (int) round($lockedTotal * 100) !== (int) round($amount * 100)) {
                throw new Exception('Cart total changed during payment processing.');
            }

            // 1. Generate 6-digit invoice number
            $invoiceNo = mt_rand(100000, 999999);

            // 2. Insert into orders table
            $stmtOrder = $this->conn->prepare(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                 VALUES (?, ?, CURDATE(), 'paid')"
            );
            if (!$stmtOrder) {
                throw new Exception('Prepare orders insert failed: ' . $this->conn->error);
            }
            $stmtOrder->bind_param('ii', $customer_id, $invoiceNo);
            if (!$stmtOrder->execute()) {
                throw new Exception('Execute orders insert failed: ' . $stmtOrder->error);
            }
            $orderId = $this->conn->insert_id;
            $stmtOrder->close();

            if (!$orderId || $orderId <= 0) {
                throw new Exception('Invalid inserted order_id.');
            }

            // 3. Copy cart items into orderdetails table
            $stmtDetail = $this->conn->prepare(
                'INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)'
            );
            if (!$stmtDetail) {
                throw new Exception('Prepare orderdetails insert failed: ' . $this->conn->error);
            }

            foreach ($lockedItems as $item) {
                $pId = (int) $item['p_id'];
                $qty = (int) $item['qty'];
                $stmtDetail->bind_param('iii', $orderId, $pId, $qty);
                if (!$stmtDetail->execute()) {
                    throw new Exception('Execute orderdetails insert failed for product ' . $pId);
                }
            }
            $stmtDetail->close();

            // 4. Record the verified reference. A UNIQUE index prevents replay races.
            // Without this column, fail and roll back rather than creating an untraceable order.
            $stmtPay = $this->conn->prepare(
                'INSERT INTO payment (amt, customer_id, order_id, currency, payment_date, payment_ref) VALUES (?, ?, ?, ?, CURDATE(), ?)'
            );
            if (!$stmtPay) {
                throw new Exception('Payment insert unavailable (check payment_ref migration): ' . $this->conn->error);
            }
            $stmtPay->bind_param('diiss', $amount, $customer_id, $orderId, $currency, $reference);
            if (!$stmtPay->execute()) {
                throw new Exception('Payment record insert failed: ' . $stmtPay->error);
            }
            $stmtPay->close();

            // 5. Delete the same IP-scoped cart that was used to calculate the total.
            // Do not delete other customers' items via OR c_id = customer_id.
            $stmtDel = $this->conn->prepare('DELETE FROM cart WHERE ip_add = ?');
            if (!$stmtDel) {
                throw new Exception('Cart clear prepare failed: ' . $this->conn->error);
            }
            $stmtDel->bind_param('s', $ip);
            if (!$stmtDel->execute()) {
                throw new Exception('Cart clear failed: ' . $stmtDel->error);
            }
            $removed = $stmtDel->affected_rows;
            $stmtDel->close();
            if ($removed !== count($cartItems)) {
                throw new Exception('Cart contents changed while completing the order.');
            }

            // Commit Transaction
            $this->conn->commit();
            return (int) $orderId;

        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('CartClass::processPayment transaction rolled back: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve complete order details by order_id.
     * Includes customer info, payment info, and purchased product items.
     *
     * @param  int $order_id
     * @return array|false
     */
    public function getOrderById($order_id)
    {
        $order_id = (int) $order_id;
        if ($order_id <= 0) {
            return false;
        }

        // 1. Fetch order + customer + payment header info
        $hasPayRefCol = false;
        $colCheck = $this->conn->query("SHOW COLUMNS FROM payment LIKE 'payment_ref'");
        if ($colCheck && $colCheck->num_rows > 0) {
            $hasPayRefCol = true;
        }

        if ($hasPayRefCol) {
            $sql = "SELECT o.*, c.customer_name, c.customer_email, c.customer_contact, c.customer_city, c.customer_country,
                           p.pay_id, p.amt AS payment_amt, p.currency AS payment_currency, p.payment_date, p.payment_ref
                    FROM orders o
                    JOIN customer c ON o.customer_id = c.customer_id
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    WHERE o.order_id = ?
                    LIMIT 1";
        } else {
            $sql = "SELECT o.*, c.customer_name, c.customer_email, c.customer_contact, c.customer_city, c.customer_country,
                           p.pay_id, p.amt AS payment_amt, p.currency AS payment_currency, p.payment_date,
                           NULL AS payment_ref
                    FROM orders o
                    JOIN customer c ON o.customer_id = c.customer_id
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    WHERE o.order_id = ?
                    LIMIT 1";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log('CartClass::getOrderById prepare failed: ' . $this->conn->error);
            return false;
        }

        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$order) {
            return false;
        }

        // 2. Fetch order items
        $stmtItems = $this->conn->prepare(
            "SELECT od.*, pr.product_title, pr.product_price, pr.product_image
             FROM orderdetails od
             JOIN products pr ON od.product_id = pr.product_id
             WHERE od.order_id = ?"
        );

        $items = [];
        if ($stmtItems) {
            $stmtItems->bind_param('i', $order_id);
            $stmtItems->execute();
            $itemsRes = $stmtItems->get_result();
            if ($itemsRes) {
                $items = $itemsRes->fetch_all(MYSQLI_ASSOC);
            }
            $stmtItems->close();
        }

        $order['items'] = $items;
        return $order;
    }
}
