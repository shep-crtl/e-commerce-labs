<?php

include __DIR__ . '/layout/header.php';

?>

<section>

    <h2>
        Welcome to ShopPN
    </h2>

    <p>
        Welcome to our e-commerce store.
    </p>

    <?php if (is_logged_in()): ?>

        <p>
            You are logged in as
            <strong>
                <?php
                echo htmlspecialchars(
                    $_SESSION['customer_name']
                );
                ?>
            </strong>.
        </p>

    <?php else: ?>

        <p>
            Please
            <a href="<?= BASE_URL ?>/views/login.php">
                login
            </a>
            or
            <a href="<?= BASE_URL ?>/views/register.php">
                create an account
            </a>
            to continue.
        </p>

    <?php endif; ?>

</section>


<?php

include __DIR__ . '/layout/footer.php';

?>