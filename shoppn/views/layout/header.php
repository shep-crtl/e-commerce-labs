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
        href="/e-commerce-labs/shoppn/css/style.css"
    >

</head>


<body>


<header>

    <div class="header-container">

        <h1>
            <a href="/e-commerce-labs/shoppn/index.php">
                ShopPN
            </a>
        </h1>


        <nav>

            <a href="/e-commerce-labs/shoppn/index.php">
                Home
            </a>


            <?php if (!is_logged_in()): ?>

                <a href="/e-commerce-labs/shoppn/views/register.php">
                    Register
                </a>

                <a href="/e-commerce-labs/shoppn/views/login.php">
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


                <a href="/e-commerce-labs/shoppn/views/account/my_account.php">
                    My Account
                </a>


                <a href="/e-commerce-labs/shoppn/logout.php">
                    Logout
                </a>


                <?php if (is_admin()): ?>

                    <a href="/e-commerce-labs/shoppn/views/admin/">
                        Admin
                    </a>

                <?php endif; ?>


            <?php endif; ?>

        </nav>

    </div>

</header>


<main class="container">