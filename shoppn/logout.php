<?php

require_once __DIR__ . '/core/core.php';

session_unset();

session_destroy();

redirect('/index.php');

?>