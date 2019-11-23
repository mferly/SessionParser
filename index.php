<?php
declare(strict_types=1);

try {
    require 'SessionParser.php';
    echo 'Number of authenticated sessions: '. (new SessionParser)::init((int)ini_get('session.gc_maxlifetime'));
} catch (\Exception $e){
    echo $e->getMessage();
}
