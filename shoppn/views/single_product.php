<?php

/*
 * views/single_product.php — Customer-facing product detail page.
 *
 * Reads ?pro_id=N from the query string, validates it, retrieves
 * the product through ProductController and renders the detail view.
 *
 * No SQL lives in this file.
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// ── Validate the product ID ──────────────────────────────────────────────────

$proId = filter_var($_GET['pro_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$proId || $proId <= 0) {
    // Missing or invalid ID → redirect to home
    redirect('/index.php');
}

// ── Retrieve the product ─────────────────────────────────────────────────────

$productController = new ProductController();
$product = $productController->getProductById($proId);

if (!$product) {
    // Product does not exist in the database → redirect to home
    $_SESSION['error'] = 'Product not found.';
    redirect('/index.php');
}

// ── Render ───────────────────────────────────────────────────────────────────

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="product-detail-section">

        <a href="<?= BASE_URL ?>/index.php" class="back-link">&larr; Back to Products</a>

        <div class="product-detail">

            <div class="product-detail-img-wrap">
                <?php if (!empty($product['product_image'])): ?>
                    <img
                        src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($product['product_image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($product['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                        class="product-detail-img"
                    >
                <?php else: ?>
                    <div class="product-img-placeholder product-img-placeholder--lg">No Image Available</div>
                <?php endif; ?>
            </div>

            <div class="product-detail-info">

                <h2 class="product-detail-title">
                    <?= htmlspecialchars($product['product_title'], ENT_QUOTES, 'UTF-8') ?>
                </h2>

                <p class="product-detail-price">
                    $<?= number_format((float) $product['product_price'], 2) ?>
                </p>

                <dl class="product-meta">

                    <dt>Category</dt>
                    <dd>
                        <?php if (!empty($product['cat_name'])): ?>
                            <a href="<?= BASE_URL ?>/index.php?cat=<?= (int) $product['product_cat'] ?>">
                                <?= htmlspecialchars($product['cat_name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </dd>

                    <dt>Brand</dt>
                    <dd>
                        <?php if (!empty($product['brand_name'])): ?>
                            <a href="<?= BASE_URL ?>/index.php?brand=<?= (int) $product['product_brand'] ?>">
                                <?= htmlspecialchars($product['brand_name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </dd>

                    <?php if (!empty($product['product_desc'])): ?>
                        <dt>Description</dt>
                        <dd><?= htmlspecialchars($product['product_desc'], ENT_QUOTES, 'UTF-8') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($product['product_keywords'])): ?>
                        <dt>Keywords</dt>
                        <dd><?= htmlspecialchars($product['product_keywords'], ENT_QUOTES, 'UTF-8') ?></dd>
                    <?php endif; ?>

                </dl>

                <a
                    href="<?= BASE_URL ?>/index.php?add_cart=<?= (int) $product['product_id'] ?>"
                    class="btn-cart btn-cart--lg"
                >
                    Add to Cart
                </a>

            </div>

        </div>

    </section>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
