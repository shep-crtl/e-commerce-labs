<?php

require_once __DIR__ . '/../../core/core.php';

require_admin();

include __DIR__ . '/../layout/header.php';

?>

<section class="admin-dashboard">

    <h2>Admin Dashboard</h2>

    <p>Welcome to the ShopPN Admin Dashboard. Choose an option below to manage the store:</p>

    <ul class="admin-links" style="list-style: none; padding: 0; margin-top: 20px;">
        <li style="margin-bottom: 15px;">
            <a href="<?= BASE_URL ?>/views/admin/brand.php" class="admin-nav-btn" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Manage Brands (Task 5 & 6)
            </a>
        </li>
        <li style="margin-bottom: 15px;">
            <a href="<?= BASE_URL ?>/views/admin/category.php" class="admin-nav-btn" style="display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Manage Categories (Task 7 & 8)
            </a>
        </li>
        <li style="margin-bottom: 15px;">
            <a href="<?= BASE_URL ?>/views/admin/product.php" class="admin-nav-btn" style="display: inline-block; padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Manage Products (Task 9)
            </a>
        </li>
    </ul>

</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
