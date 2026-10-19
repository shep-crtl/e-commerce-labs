<?php

require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductClass();
    }

    // ==========================================
    // BRAND METHODS
    // ==========================================

    public function addBrand($name)
    {
        return $this->productModel->addBrand($name);
    }

    public function getAllBrands()
    {
        return $this->productModel->getAllBrands();
    }

    public function getBrandById($id)
    {
        return $this->productModel->getBrandById($id);
    }

    public function updateBrand($id, $name)
    {
        return $this->productModel->updateBrand($id, $name);
    }

    // ==========================================
    // CATEGORY METHODS
    // ==========================================

    public function addCategory($name)
    {
        return $this->productModel->addCategory($name);
    }

    public function getAllCategories()
    {
        return $this->productModel->getAllCategories();
    }

    public function getCategoryById($id)
    {
        return $this->productModel->getCategoryById($id);
    }

    public function updateCategory($id, $name)
    {
        return $this->productModel->updateCategory($id, $name);
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
        return $this->productModel->addProduct(
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );
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
        return $this->productModel->updateProduct(
            $id,
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );
    }

    public function getProductById($id)
    {
        return $this->productModel->getProductById($id);
    }

    public function getAllProducts()
    {
        return $this->productModel->getAllProducts();
    }

    // ==========================================
    // PRODUCT METHODS (Task 10) — Display, Search, Filter
    // ==========================================

    /**
     * Return up to $limit featured products (random order).
     *
     * @param  int  $limit
     * @return array
     */
    public function getFeaturedProducts($limit = 6)
    {
        return $this->productModel->getFeaturedProducts($limit);
    }

    /**
     * Return all products in a given category.
     *
     * @param  int  $cat_id
     * @return array
     */
    public function getProductsByCategory($cat_id)
    {
        return $this->productModel->getProductsByCategory($cat_id);
    }

    /**
     * Return all products for a given brand.
     *
     * @param  int  $brand_id
     * @return array
     */
    public function getProductsByBrand($brand_id)
    {
        return $this->productModel->getProductsByBrand($brand_id);
    }

    /**
     * Search products by title and keywords.
     *
     * @param  string  $query
     * @return array
     */
    public function searchProducts($query)
    {
        return $this->productModel->searchProducts($query);
    }
}
