<?php

/*
 * Sidebar: category and brand filter links.
 *
 * ProductController is already instantiated in the calling view
 * (home.php, single_product.php, search_results.php) and passed
 * as $productController. We reuse it here to avoid a second DB
 * connection. If for any reason it is not set, we instantiate it
 * defensively.
 */
if (!isset($productController)) {
    require_once __DIR__ . '/../../controllers/ProductController.php';
    $productController = new ProductController();
}

$sidebarCategories = $productController->getAllCategories();
$sidebarBrands     = $productController->getAllBrands();

?>

<aside class="sidebar">

    <div class="sidebar-section">
        <h3>Categories</h3>

        <?php if (empty($sidebarCategories)): ?>
            <p class="sidebar-empty">No categories yet.</p>
        <?php else: ?>
            <ul class="sidebar-list">
                <?php foreach ($sidebarCategories as $cat): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?cat=<?= (int) $cat['cat_id'] ?>">
                            <?= htmlspecialchars($cat['cat_name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="sidebar-section">
        <h3>Brands</h3>

        <?php if (empty($sidebarBrands)): ?>
            <p class="sidebar-empty">No brands yet.</p>
        <?php else: ?>
            <ul class="sidebar-list">
                <?php foreach ($sidebarBrands as $brand): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?brand=<?= (int) $brand['brand_id'] ?>">
                            <?= htmlspecialchars($brand['brand_name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</aside>
