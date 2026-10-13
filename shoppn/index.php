<?php

/*
 * index.php — Application entry point.
 *
 * Bootstraps core (session, BASE_URL, helpers) then routes to
 * the appropriate view based on GET parameters.
 *
 * Routing:
 *   ?user_query=X  → search results displayed inline on home.php
 *   ?cat=N         → category filter displayed inline on home.php
 *   ?brand=N       → brand filter displayed inline on home.php
 *   (none)         → featured products on home.php
 *
 * Note: ?add_cart=N is reserved for Task 11 and is ignored here.
 */

require_once __DIR__ . '/core/core.php';

include __DIR__ . '/views/home.php';