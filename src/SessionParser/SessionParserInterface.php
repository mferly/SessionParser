<?php
declare(strict_types=1);

namespace SessionParser;

interface SessionParserInterface {

    /**
     * Initializes the program.
     *
     * @param int $sessionGcMaxlifetime Attempt to retrieve 'session.gc_maxlifetime' from php.ini
     * @param string $sessionFolderPath Path to directory containing session files.
     * @method static int
     * @return int
     */
    public static function init(string $sessionFolderPath): int;

    /**
     * Iterates through $sessionFolderPath and calls fileParser()
     *
     * @method static int
     * @return $counter int on success, null on error.
     */
    public static function directoryIterator(): int;

    /**
     * Parses arg looking for $needle
     *
     * @param string $sessionString
     * @example: array(2) {
     *      [0]=> string(28) "user|s:6:"mferly";auth|b:0; "
     *      [1]=> string(34) "user|s:11:"my_username";auth|b:1; "
     * }
     * @example:
     *   auth|b:0 = un-authenticated session
     *   auth|b:1 = authenticated session
     * @method static bool
     * @return bool
     */
    public static function fileParser(string $sessionString): bool;
}
