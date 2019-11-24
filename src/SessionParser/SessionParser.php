<?php
declare(strict_types=1);

namespace SessionParser;

/**
 * Class to parse session files for determining user authenticated sessions.
 *
 * @category Software
 * @author   mferly <mferly@example.com>
 * @license  MIT (https://opensource.org/licenses/MIT)
 * @file     SessionParser.php
 * @link     https://www.reddit.com/user/mferly
 *
 * @uses     interface SessionParserInterface
 */
final class SessionParser implements SessionParserInterface
{

    /**
     * A placeholder to hold the file iterator so that directory traversal is only
     * performed once.
     */
    private static $fileIterator = null;

    /** Holds iterative counter of authenticated sessions */
    private static $counter = 0;

    /**
     * This is the matching string per the session file to determine whether the
     * session is authenticated or not.
     *
     * @var string static
     * @example: $_SESSION['auth'] = 1; writes 'auth|b:1' to a session file
     *              located in the path you've set in static::$sessionFolderPath.
     *           $_SESSION['auth'] = 0; writes (updates) that session file to
     *              reflect that change: auth|b:0
     *           Replace $_SESSION['auth'] with your program's $_SESSION key/index
     *              ie. $_SESSION['authenticated'] = true, etc.
     *           Update static::$needle to reflect
     * @example: array(2) {
     *      [0]=> string(28) "user|s:6:"mferly";auth|b:0; "
     *      [1]=> string(34) "user|s:11:"my_username";auth|b:1; "
     * }
     * @example:
     *   auth|b:0 = un-authenticated session
     *   auth|b:1 = authenticated session
     */
    private static $needle = 'auth|b:1';

    /**
     * Path to the directory you want to iterate on.
     *
     * @var string
     */
    private static $sessionFolderPath = '/var/lib/php/sessions/';

    /**
     * This is used to check length (in time()) of authenticated session.
     *
     * Its value should be coming from php.ini config automagically but we assign
     * a default just in case. We use this to check length (in time()) of
     * authenticated session.
     *
     * This default value is in place in the case that your ENV is blocking you
     * from retrieving ini_get('session.gc_maxlifetime'). This default value
     * should be the same value as seen in php.ini
     *
     * Note: this property value is in seconds.
     *
     * @var int
     */
    private static $sessionGcMaxlifetime = 1440;

    /**
     * Initializes the program.
     *
     * @param int $sessionGcMaxlifetime Attempt to retrieve 'session.gc_maxlifetime' from php.ini
     * @param string $sessionFolderPath Path to directory containing session files.
     * @method static int
     * @return int
     */
    public static function init(string $sessionFolderPath = ''): int
    {
        if (function_exists('ini_get')) {
            static::$sessionGcMaxlifetime = (int) @ini_get('session.gc_maxlifetime');
        }

        if (!empty($sessionFolderPath)) static::$sessionFolderPath = $sessionFolderPath;

        return static::directoryIterator();
    }

    /**
     * Iterates through $sessionFolderPath and calls fileParser()
     *
     * @return int
     * @throws \Exception
     */
    public static function directoryIterator(): int
    {
        try {
            static::$fileIterator = new \RecursiveIteratorIterator(
                (new \RecursiveDirectoryIterator(
                    static::$sessionFolderPath,
                    \RecursiveDirectoryIterator::SKIP_DOTS)
                ), \RecursiveIteratorIterator::LEAVES_ONLY);

            foreach (static::$fileIterator as $file) {
                if ($file->isReadable()) {
                    if ($file->getSize() > 0) {

                        $handle = fopen($file->getPathname(), 'r');
                        $sessionString = fread($handle, $file->getSize());
                        fclose($handle);

                        if (
                            false !== static::fileParser($sessionString)
                            && time() - $file->getMTime() <= static::$sessionGcMaxlifetime
                        ) {
                            static::$counter++;
                        }
                    }
                } else {
                    throw new \Exception("
                        <p>$file <b>is not readable</b>.</p>
                        <p>Check file/folder permissions/ownership.</p>
                    ");
                    break;
                }
            }
        } catch(\Exception $e) {
            throw new \Exception('
                <p><b>' . static::$sessionFolderPath . '</b> is not accessible</b>.</p>
                <p>Check folder permissions/ownership.</p>
                <p>Exception: '. $e->getMessage() . '</p>
            ');
        }
        return static::$counter;
    }

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
    public static function fileParser(string $sessionString = ''): bool
    {
        if (
            !empty($sessionString)
            && false !== strpos($sessionString, static::$needle)
        ) {
            return true;
        }
        return false;
    }
}
