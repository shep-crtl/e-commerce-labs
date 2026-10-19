<?php

/*
 * index.php — Application entry point.
 *
 * Bootstraps core (session, BASE_URL, helpers) then routes to
 * the appropriate view or action based on GET parameters.
 *
 * Routing:
 *   ?add_cart=N    → routes to add_to_cart_action.php (Task 11)
 *   ?user_query=X  → search results displayed inline on home.php
 *   ?cat=N         → category filter displayed inline on home.php
 *   ?brand=N       → brand filter displayed inline on home.php
 *   (none)         → featured products on home.php
 */

require_once __DIR__ . '/core/core.php';

// Route add_cart link to action file (Task 11)
if (isset($_GET['add_cart'])) {
    $addCartId = (int) $_GET['add_cart'];
    redirect('/actions/add_to_cart_action.php?add_cart=' . $addCartId);
}

include __DIR__ . '/views/home.php';