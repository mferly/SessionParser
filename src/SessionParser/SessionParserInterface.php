<?php
declare(strict_types=1);

namespace SessionParser;

interface SessionParserInterface {

    /** Initializes the program. */
    public static function init(string $sessionFolderPath): int;

    /** Iterates through chosen directory */
    public static function fileIterator(): int;

    /** Parses arg looking for needle */
    public static function fileParser(string $sessionString): bool;
}
