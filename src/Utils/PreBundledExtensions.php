<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Utils;

class PreBundledExtensions
{
    private static array $preBundledExtensions = [
        'core',
        'ctype',
        'curl',
        'date',
        'dom',
        'fileinfo',
        'filter',
        'ftp',
        'hash',
        'iconv',
        'json',
        'libxml',
        'mbstring',
        'mysqlnd',
        'openssl',
        'pcre',
        'pdo',
        'pdo_sqlite',
        'phar',
        'posix',
        'readline',
        'reflection',
        'session',
        'simplexml',
        'sodium',
        'spl',
        'sqlite3',
        'standard',
        'tokenizer',
        'xml',
        'xmlreader',
        'xmlwriter',
        'zlib',
    ];

    public static function getExtensions(): array
    {
        return self::$preBundledExtensions;
    }
}
