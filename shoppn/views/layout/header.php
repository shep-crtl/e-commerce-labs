<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

// Instantiate CartController to fetch live cart count and total price (Task 11)
$headerCartController = new CartController();
$headerCartIp = get_ip();
$headerCartCount = $headerCartController->getCartCount($headerCartIp);
$headerCartTotal = $headerCartController->getCartTotal($headerCartIp);

?>
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


        <!-- Search form — submits to index.php with user_query parameter -->
        <form
            class="header-search"
            action="<?= BASE_URL ?>/index.php"
            method="GET"
            role="search"
        >
            <input
                type="text"
                name="user_query"
                id="header-search-input"
                placeholder="Search products..."
                aria-label="Search products"
                value="<?= isset($_GET['user_query']) ? htmlspecialchars($_GET['user_query'], ENT_QUOTES, 'UTF-8') : '' ?>"
            >
            <button type="submit" aria-label="Submit search">Search</button>
        </form>


        <nav>

            <a href="<?= BASE_URL ?>/index.php">
                Home
            </a>

            <a href="<?= BASE_URL ?>/views/cart.php">
                Cart (<?= (int) $headerCartCount ?>)
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

                    <a href="<?= BASE_URL ?>/views/admin/brand.php">
                        Brands
                    </a>

                    <a href="<?= BASE_URL ?>/views/admin/category.php">
                        Categories
                    </a>

                    <a href="<?= BASE_URL ?>/views/admin/product.php">
                        Products
                    </a>

                <?php endif; ?>


            <?php endif; ?>

        </nav>

    </div>

    <!-- Task 11: Cart summary bar required by lab specification -->
    <div class="cart-summary-bar">
        <div class="cart-summary-inner">
            <span class="cart-summary-text">
                Shopping Cart &mdash; Total Items: <strong><?= (int) $headerCartCount ?></strong> | Total Price: <strong>$<?= number_format((float) $headerCartTotal, 2) ?></strong>
            </span>
            <a href="<?= BASE_URL ?>/views/cart.php" class="cart-summary-btn">
                Go to Cart &rarr;
            </a>
        </div>
    </div>

</header>


<main class="container">