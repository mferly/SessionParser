<?php
declare(strict_types=1);

interface SessionParserInterface {

    public static function init(int $sessionGcMaxlifetime, string $sessionFolderPath): int;
    public static function fileIterator(): int;
    public static function fileParser(string $sessionString): bool;
}

/**
 * Class to parse session files for determining user authenticated sessions.
 */
final class SessionParser implements SessionParserInterface
{

    /**
     * @name $fileIterator
     * @scope private
     * @type null static
     * @default null
     * @description: A placeholder to hold the file iterator so that directory traversal is only
     *               performed once.
     */
    private static $fileIterator = null;

    /**
     * @name $counter
     * @scope private
     * @type int static
     * @description: holds counter of authenticated sessions
     */
    private static $counter = 0;

    /**
     * @name $needle
     * @scope private
     * @type string static
     * @description: this is the matching string per the session file to determine
     *               whether the session is authenticated or not.
     * @usage:  $_SESSION['auth'] = 1; writes 'auth|b:1' to a session file
     *              located in the path you've set in static::$sessionFolderPath.
     *          $_SESSION['auth'] = 0; writes (updates) that session file to
     *              reflect that change: auth|b:0
     * @replace $_SESSION['auth'] with your program's $_SESSION key/index
     *              ie. $_SESSION['authenticated'] = true, etc.
     *          Update static::$needle to reflect
     */
    private static $needle = 'auth|b:1';

    /**
     * @name $sessionFolderPath
     * @scope private
     * @type string
     * @description: path to the folder you want to iterate on
     * @message: /var/lib/php/sessions is very much likely to be (should be) root access only.
     *           Executing this script via browser (will not) should not work.
     *           Execute this script via CLI only.
     */
    private static $sessionFolderPath = '/var/lib/php/sessions/';

    /**
     * @name $sessionGcMaxlifetime
     * @scope private
     * @type int
     * @default 3600 seconds (1 hour) This should be overwritten by
     *                                ini_get('session.gc_maxlifetime')
     *                                as long as ini_get() is available per ENV.
     * @description: This is used to check length (in time()) of authenticated session.
     *               This should be coming from php.ini config automagically as
     *               requested in index.php
     */
    private static $sessionGcMaxlifetime = 3600;

    /**
     * @name init
     * @scope public
     * @type static method
     * @param $sessionFolderPath string
     * @return int static::fileIterator()
     */
    public static function init(int $sessionGcMaxlifetime, string $sessionFolderPath = ''): int
    {
        if (!empty($sessionGcMaxlifetime)) static::$sessionGcMaxlifetime = $sessionGcMaxlifetime;
        if (!empty($sessionFolderPath)) static::$sessionFolderPath = $sessionFolderPath;

        return static::fileIterator();
    }

    /**
     * @name fileIterator()
     * @scope public
     * @type static method
     * @description: iterates through static::$sessionFolderPath folder
     * @return $counter int on success, null on error.
     */
    public static function fileIterator(): int
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

                        if (false !== static::fileParser($sessionString)) {
                            if (time() - $file->getMTime() <= static::$sessionGcMaxlifetime) {
                                static::$counter++;
                            }
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
     * @name fileParser
     * @scope public
     * @type static method
     * @param $sessionString string default empty
     * @example: array(2) {
     *      [0]=> string(28) "user|s:6:"mferly";auth|b:0; "
     *      [1]=> string(34) "user|s:11:"my_username";auth|b:1; "
     * }
     * @explain:
     *   auth|b:0 = un-authenticated session
     *   auth|b:1 = authenticated session
     * @return bool
     */
    public static function fileParser(string $sessionString = ''): bool
    {
        if (!empty($sessionString)) {
            if (false !== strpos($sessionString, static::$needle)) {
                return true;
            }
        }
        return false;
    }
}
