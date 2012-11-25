<?php

require 'vendor/autoload.php';

echo "Running demo...\n";

$_SERVER['HTTP_HOST'] = 'example.org';

$statWriter = new Heapstersoft\Stats\Writer('config/stats.yml');

$statWriter->increment('key1');