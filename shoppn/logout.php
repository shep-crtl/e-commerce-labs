<?php

session_start();

session_unset();

session_destroy();

header(
    'Location: /e-commerce-labs/shoppn/index.php'
);

exit();

?>