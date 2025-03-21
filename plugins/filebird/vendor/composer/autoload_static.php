<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit802ef26fd7ad065e7fc5a3688ee356a4
{
    public static $prefixLengthsPsr4 = array (
        'e' => 
        array (
            'enshrined\\svgSanitize\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'enshrined\\svgSanitize\\' => 
        array (
            0 => __DIR__ . '/..' . '/enshrined/svg-sanitize/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit802ef26fd7ad065e7fc5a3688ee356a4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit802ef26fd7ad065e7fc5a3688ee356a4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit802ef26fd7ad065e7fc5a3688ee356a4::$classMap;

        }, null, ClassLoader::class);
    }
}
