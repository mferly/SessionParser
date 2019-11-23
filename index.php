<?php
declare(strict_types=1);

try {
    require './src/autoload.php';

    echo 'Number of authenticated sessions: '. (new \SessionParser\SessionParser)::init();
} catch (\Exception $e){
    echo $e->getMessage();
}
