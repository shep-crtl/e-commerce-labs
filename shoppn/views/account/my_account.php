<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

include __DIR__ . '/../layout/header.php';

?>


<h2>
    My Account
</h2>


<p>

    Welcome,
    <strong>
        <?php
        echo htmlspecialchars(
            $_SESSION['customer_name']
        );
        ?>
    </strong>

</p>


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