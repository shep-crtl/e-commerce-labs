<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

include __DIR__ . '/../layout/header.php';

?>


<h2>
    My Account
</h2>

<?php $avatarUrl = get_customer_image_url($_SESSION['customer_image'] ?? null); ?>
<div class="account-profile-header" style="display: flex; gap: 20px; align-items: center; margin: 20px 0; padding: 15px; background: #fafafa; border: 1px solid #eee; border-radius: 6px;">
    <?php if ($avatarUrl): ?>
        <img
            src="<?= $avatarUrl ?>"
            alt="Profile Avatar"
            style="width: 75px; height: 75px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e0e0; background: #fff;"
        >
    <?php endif; ?>
    <div>
        <p style="margin: 0 0 5px; font-size: 1.1rem;">
            Welcome, <strong><?= htmlspecialchars($_SESSION['customer_name']) ?></strong>
        </p>
        <span style="display: inline-block; padding: 3px 10px; background: <?= is_admin() ? '#cfe2ff' : '#d1e7dd' ?>; color: <?= is_admin() ? '#084298' : '#0f5132' ?>; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">
            <?= is_admin() ? 'Administrator' : 'Customer Account' ?>
        </span>
    </div>
</div>

<div class="account-info">

    <p>
        <strong>Customer ID:</strong>

        <?php
        echo htmlspecialchars(
            $_SESSION['customer_id']
        );
        ?>

    </p>


    <p>
        <strong>Email:</strong>

        <?php
        echo htmlspecialchars(
            $_SESSION['customer_email']
        );
        ?>

    </p>


    <p>

        <strong>Account Type:</strong>

        <?php

        if (is_admin()) {
            echo 'Administrator';
        } else {
            echo 'Customer';
        }

        ?>

    </p>

</div>


<p>

    <a href="<?= BASE_URL ?>/logout.php">
        Logout
    </a>

</p>


<?php

include __DIR__ . '/../layout/footer.php';

?>