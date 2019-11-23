<?php
declare(strict_types=1);

require './src/autoload.php';

try {
    echo 'Number of authenticated sessions: '. (new \SessionParser\SessionParser)::init();
} catch (\Exception $e) {
    echo $e->getMessage();
}
