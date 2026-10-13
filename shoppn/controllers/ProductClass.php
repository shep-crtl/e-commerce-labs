<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    // ==========================================
    // BRAND METHODS (Tasks 5 & 6)
    // ==========================================

    public function addBrand($name)
    {
        $stmt = $this->conn->prepare('INSERT INTO brands (brand_name) VALUES (?)');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $name);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAllBrands()
    {
        $stmt = $this->conn->prepare('SELECT * FROM brands ORDER BY brand_name ASC');
        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $brands = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $brands;
    }

    public function getBrandById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM brands WHERE brand_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: false;
    }

    public function updateBrand($id, $name)
    {
        $stmt = $this->conn->prepare('UPDATE brands SET brand_name = ? WHERE brand_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('si', $name, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }


    // ==========================================
    // CATEGORY METHODS (Tasks 7 & 8)
    // ==========================================

    public function addCategory($name)
    {
        $stmt = $this->conn->prepare('INSERT INTO categories (cat_name) VALUES (?)');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $name);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAllCategories()
    {
        $stmt = $this->conn->prepare('SELECT * FROM categories ORDER BY cat_name ASC');
        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $categories;
    }

    public function getCategoryById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM categories WHERE cat_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: false;
    }

    public function updateCategory($id, $name)
    {
        $stmt = $this->conn->prepare('UPDATE categories SET cat_name = ? WHERE cat_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('si', $name, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }


    // ==========================================
    // PRODUCT METHODS (Task 9)
    // ==========================================

    public function addProduct(
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $stmt = $this->conn->prepare(
            'INSERT INTO products
            (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'iisdsss',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function updateProduct(
        $id,
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $stmt = $this->conn->prepare(
            'UPDATE products
             SET product_cat = ?,
                 product_brand = ?,
                 product_title = ?,
                 product_price = ?,
                 product_desc = ?,
                 product_image = ?,
                 product_keywords = ?
             WHERE product_id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            'iisdsssi',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords,
            $id
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getProductById($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_id = ?'
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: false;
    }

    public function getAllProducts()
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             ORDER BY p.product_id DESC'
        );

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }


    // ==========================================
    // PRODUCT METHODS (Task 10) — Display, Search, Filter
    // ==========================================

    /**
     * Return up to $limit random featured products.
     * ORDER BY RAND() is required by the lab spec.
     * The LIMIT value is an integer cast server-side and interpolated
     * into the query because MySQLi cannot bind LIMIT placeholders.
     */
    public function getFeaturedProducts($limit = 6)
    {
        // Ensure limit is a safe positive integer before interpolation.
        $safeLimit = max(1, (int) $limit);

        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             ORDER BY RAND()
             LIMIT ' . $safeLimit
        );

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    /**
     * Return all products belonging to a given category.
     *
     * @param  int  $cat_id  Must be a positive integer.
     * @return array
     */
    public function getProductsByCategory($cat_id)
    {
        $cat_id = (int) $cat_id;
        if ($cat_id <= 0) {
            return [];
        }

        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_cat = ?
             ORDER BY p.product_title ASC'
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    /**
     * Return all products belonging to a given brand.
     *
     * @param  int  $brand_id  Must be a positive integer.
     * @return array
     */
    public function getProductsByBrand($brand_id)
    {
        $brand_id = (int) $brand_id;
        if ($brand_id <= 0) {
            return [];
        }

        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_brand = ?
             ORDER BY p.product_title ASC'
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    /**
     * Full-text LIKE search across product_title and product_keywords.
     *
     * @param  string  $query  Raw search string from user input.
     * @return array
     */
    public function searchProducts($query)
    {
        $query = trim((string) $query);
        if ($query === '') {
            return [];
        }

        // Wrap the search term in % wildcards for LIKE matching.
        $pattern = '%' . $query . '%';

        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             LEFT JOIN categories c ON p.product_cat = c.cat_id
             LEFT JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_title LIKE ?
                OR p.product_keywords LIKE ?
             ORDER BY p.product_title ASC'
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('ss', $pattern, $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }
}
