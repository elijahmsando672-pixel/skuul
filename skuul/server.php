<?php

if (php_sapi_name() !== 'cli-server') {
    return false;
}

__DIR__ . '/index.php';