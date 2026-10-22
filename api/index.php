<?php

$views = '/tmp/storage/framework/views';
if (!is_dir($views)) {
    mkdir($views, 0777, true);
}

require __DIR__ . '/../public/index.php';
