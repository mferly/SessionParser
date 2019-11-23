<?php
declare(strict_types=1);

namespace SessionParser;

interface SessionParserInterface {

    public static function init(int $sessionGcMaxlifetime, string $sessionFolderPath): int;
    public static function fileIterator(): int;
    public static function fileParser(string $sessionString): bool;
}
