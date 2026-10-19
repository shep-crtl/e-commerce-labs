<?php

/*
 * views/home.php — Customer-facing home page.
 *
 * Handles three display modes depending on GET parameters:
 *   ?cat=N        → category filter
 *   ?brand=N      → brand filter
 *   ?user_query=X → search results (redirected here from header form)
 *   (none)        → featured products
 *
 * core.php and session_start() are already called by index.php
 * before this file is included.
 */

require_once __DIR__ . '/../controllers/ProductController.php';

$productController = new ProductController();

// ── Determine display mode ──────────────────────────────────────────────────

$products    = [];
$pageHeading = 'Featured Products';
$isSearch    = false;
$searchTerm  = '';

if (isset($_GET['user_query']) && trim($_GET['user_query']) !== '') {
    // Search mode
    $isSearch   = true;
    $searchTerm = trim($_GET['user_query']);
    $products   = $productController->searchProducts($searchTerm);
    $pageHeading = 'Search Results for: ' . htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');

} elseif (isset($_GET['cat'])) {
    // Category filter mode
    $catId = filter_var($_GET['cat'], FILTER_VALIDATE_INT);
    if ($catId && $catId > 0) {
        $catInfo = $productController->getCategoryById($catId);
        $products = $productController->getProductsByCategory($catId);
        $pageHeading = $catInfo
            ? 'Category: ' . htmlspecialchars($catInfo['cat_name'], ENT_QUOTES, 'UTF-8')
            : 'Category Products';
    } else {
        // Invalid cat ID → show featured instead
        $products    = $productController->getFeaturedProducts(6);
        $pageHeading = 'Featured Products';
    }

} elseif (isset($_GET['brand'])) {
    // Brand filter mode
    $brandId = filter_var($_GET['brand'], FILTER_VALIDATE_INT);
    if ($brandId && $brandId > 0) {
        $brandInfo = $productController->getBrandById($brandId);
        $products  = $productController->getProductsByBrand($brandId);
        $pageHeading = $brandInfo
            ? 'Brand: ' . htmlspecialchars($brandInfo['brand_name'], ENT_QUOTES, 'UTF-8')
            : 'Brand Products';
    } else {
        $products    = $productController->getFeaturedProducts(6);
        $pageHeading = 'Featured Products';
    }

} else {
    // Default: featured products
    $products    = $productController->getFeaturedProducts(6);
    $pageHeading = 'Featured Products';
}

// ── Render ──────────────────────────────────────────────────────────────────

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="product-section">

        <h2><?= $pageHeading ?></h2>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success">
                <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error">
                <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (empty($products)): ?>

            <?php if ($isSearch): ?>
                <p class="empty-state">
                    No products matched your search for
                    <strong><?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?></strong>.
                    Try different keywords, or <a href="<?= BASE_URL ?>/index.php">browse all products</a>.
                </p>
            <?php else: ?>
                <p class="empty-state">
                    No products found. Please check back later.
                </p>
            <?php endif; ?>

        <?php else: ?>

            <div class="product-grid">

                <?php foreach ($products as $prod): ?>

                    <div class="product-card">

                        <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $prod['product_id'] ?>" class="product-card-img-link">
                            <?php if (!empty($prod['product_image'])): ?>
                                <img
                                    src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($prod['product_image'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($prod['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="product-img"
                                >
                            <?php else: ?>
                                <div class="product-img-placeholder">No Image</div>
                            <?php endif; ?>
                        </a>

                        <div class="product-card-body">

                            <h3 class="product-title">
                                <?= htmlspecialchars($prod['product_title'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="product-price">
                                $<?= number_format((float) $prod['product_price'], 2) ?>
                            </p>

                            <div class="product-card-actions">
                                <a
                                    href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $prod['product_id'] ?>"
                                    class="btn-details"
                                >
                                    Details
                                </a>

                                <a
                                    href="<?= BASE_URL ?>/actions/add_to_cart_action.php?add_cart=<?= (int) $prod['product_id'] ?>"
                                    class="btn-cart"
                                >
                                    Add to Cart
                                </a>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>