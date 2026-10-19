<?php

/*
 * views/search_results.php — Search results page.
 *
 * Reads ?user_query=X from GET, validates and trims it,
 * then delegates to ProductController->searchProducts().
 *
 * This page is reached when the user submits the header search
 * form. The home page also handles search inline, but this
 * dedicated view is provided per the lab specification.
 *
 * No SQL lives in this file.
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// ── Read and validate the search query ───────────────────────────────────────

$rawQuery = $_GET['user_query'] ?? '';
$searchTerm = trim((string) $rawQuery);

$products = [];

if ($searchTerm !== '') {
    $productController = new ProductController();
    $products = $productController->searchProducts($searchTerm);
} else {
    // If query is empty, redirect home gracefully
    redirect('/index.php');
}

// ── Render ───────────────────────────────────────────────────────────────────

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="product-section">

        <h2>
            Search Results for:
            <em><?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?></em>
        </h2>

        <?php if (empty($products)): ?>

            <p class="empty-state">
                No products matched
                <strong><?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?></strong>.
                Try a different keyword, or
                <a href="<?= BASE_URL ?>/index.php">browse all products</a>.
            </p>

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
