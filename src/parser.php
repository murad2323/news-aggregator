<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\RssParser;

try {
    $parser = new RssParser();
    $parser->run();
} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}