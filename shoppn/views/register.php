<?php

require_once __DIR__ . '/../core/core.php';

include __DIR__ . '/layout/header.php';

?>

<h2>Create an Account</h2>


<?php if (isset($_SESSION['error'])): ?>

    <p class="error">
        <?php
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        ?>
    </p>

<?php endif; ?>


<form
    id="register-form"
    action="<?= BASE_URL ?>/actions/register_action.php"
    method="POST"
>

    <div class="form-group">

        <label for="name">
            Full Name
        </label>

        <input
            type="text"
            name="name"
            id="name"
            maxlength="100"
            required
        >

        <span
            id="name-error"
            class="field-error"
        ></span>

    </div>


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

        <span
            id="email-error"
            class="field-error"
        ></span>

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

        <span
            id="password-error"
            class="field-error"
        ></span>

    </div>


    <div class="form-group">

        <label for="country">
            Country
        </label>

        <select
            name="country"
            id="country"
            required
        >

            <option value="">
                Select Country
            </option>

            <option value="Ghana">
                Ghana
            </option>

            <option value="Nigeria">
                Nigeria
            </option>

            <option value="Kenya">
                Kenya
            </option>

            <option value="South Africa">
                South Africa
            </option>

        </select>

        <span
            id="country-error"
            class="field-error"
        ></span>

    </div>


    <div class="form-group">

        <label for="city">
            City
        </label>

        <input
            type="text"
            name="city"
            id="city"
            maxlength="30"
            required
        >

        <span
            id="city-error"
            class="field-error"
        ></span>

    </div>


    <div class="form-group">

        <label for="contact">
            Contact Number
        </label>

        <input
            type="text"
            name="contact"
            id="contact"
            maxlength="15"
            required
        >

        <span
            id="contact-error"
            class="field-error"
        ></span>

    </div>


    <button type="submit">
        Register
    </button>

</form>


<p>
    Already have an account?

    <a href="<?= BASE_URL ?>/views/login.php">
        Login
    </a>
</p>


<script src="<?= BASE_URL ?>/js/validate.js"></script>


<?php include __DIR__ . '/layout/footer.php'; ?>