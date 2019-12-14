<?php
declare(strict_types=1);

/**
 * Class to parse session files for determining user authenticated sessions.
 *
 * PHP version 7.3.11-1+ubuntu18.04.1+deb.sury.org+1
 *
 * @category Software
 * @author   mferly <mferly@example.com>
 * @license  MIT (https://opensource.org/licenses/MIT)
 * @file     index.php
 * @link     https://www.reddit.com/user/mferly
 */

require './src/autoload.php';

try {
    echo 'Number of authenticated sessions: '. (new \SessionParser\SessionParser)::init('/var/lib/php/sessions/');
} catch (\Exception $e) {
    echo $e->getMessage();
}
