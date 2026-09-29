<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ShopPN</title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/css/style.css"
    >

</head>


<body>


<header>

    <div class="header-container">

        <h1>
            <a href="<?= BASE_URL ?>/index.php">
                ShopPN
            </a>
        </h1>


        <nav>

            <a href="<?= BASE_URL ?>/index.php">
                Home
            </a>


            <?php if (!is_logged_in()): ?>

                <a href="<?= BASE_URL ?>/views/register.php">
                    Register
                </a>

                <a href="<?= BASE_URL ?>/views/login.php">
                    Login
                </a>


            <?php else: ?>

                <span class="welcome">

                    Welcome,
                    <?php
                    echo htmlspecialchars(
                        $_SESSION['customer_name']
                    );
                    ?>

                </span>


                <a href="<?= BASE_URL ?>/views/account/my_account.php">
                    My Account
                </a>


                <a href="<?= BASE_URL ?>/logout.php">
                    Logout
                </a>


                <?php if (is_admin()): ?>

                    <a href="<?= BASE_URL ?>/views/admin/">
                        Admin
                    </a>

                <?php endif; ?>


            <?php endif; ?>

        </nav>

    </div>

</header>


<main class="container">