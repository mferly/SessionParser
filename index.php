<?php
declare(strict_types=1);

try {
    require './SessionParser.php';

    $sessionGcMaxlifetime = (int) @ini_get('session.gc_maxlifetime');
    if (empty($sessionGcMaxlifetime)) $sessionGcMaxlifetime = 0;

    echo 'Number of authenticated sessions: '. (new SessionParser)::init($sessionGcMaxlifetime);
} catch (\Exception $e){
    echo $e->getMessage();
}
