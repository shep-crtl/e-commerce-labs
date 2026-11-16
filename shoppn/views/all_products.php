<?php

/*
 * views/all_products.php — Customer-facing catalog of all available products.
 *
 * Calls ProductController->getAllProducts() and displays items in responsive grid.
 * Robust image fallback using get_product_image_url().
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$productController = new ProductController();
$products = $productController->getAllProducts();

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="product-section">

        <h2>All Products</h2>

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
            <p class="empty-state">
                No products found. Please check back later.
            </p>
        <?php else: ?>

            <div class="product-grid">

                <?php foreach ($products as $prod): ?>
                    <?php
                    $imgUrl = get_product_image_url($prod['product_image'] ?? '');
                    ?>
                    <div class="product-card">

                        <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $prod['product_id'] ?>" class="product-card-img-link">
                            <?php if ($imgUrl): ?>
                                <img
                                    src="<?= $imgUrl ?>"
                                    alt="<?= htmlspecialchars($prod['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="product-img"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="product-img-placeholder" style="display: none;">No Image</div>
                            <?php else: ?>
                                <div class="product-img-placeholder">No Image</div>
                            <?php endif; ?>
                        </a>

                        <div class="product-card-body">

                            <h3 class="product-title">
                                <?= htmlspecialchars($prod['product_title'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="product-price">
                                GH₵ <?= number_format((float) $prod['product_price'], 2) ?>
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
