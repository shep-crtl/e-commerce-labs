<?php

require_once __DIR__ . '/../core/core.php';

include __DIR__ . '/layout/header.php';

?>

<h2>Login</h2>


<?php if (isset($_SESSION['error'])): ?>

    <p class="error">

        <?php

        echo htmlspecialchars($_SESSION['error']);

        unset($_SESSION['error']);

        ?>

    </p>

<?php endif; ?>


<form
    action="<?= BASE_URL ?>/actions/login_action.php"
    method="POST"
>

    <div class="form-group">

        <label for="email">
            Email
        </label>

        <input
            type="email"
            name="email"
            id="email"
            maxlength="50"
            required
        >

    </div>


    <div class="form-group">

        <label for="password">
            Password
        </label>

        <input
            type="password"
            name="password"
            id="password"
            required
        >

    </div>


    <button type="submit">
        Login
    </button>

</form>


<p>

    Don't have an account?

    <a href="<?= BASE_URL ?>/views/register.php">
        Register
    </a>

</p>


<?php include __DIR__ . '/layout/footer.php'; ?>