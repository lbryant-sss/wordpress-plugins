<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcb805c9fadce0583c03c04522eed9724
{
    public static $files = array (
        '08eca214f4d3690babeee667e1bd7ede' => __DIR__ . '/../..' . '/src/php/includes/deprecated.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'CyrToLat\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'CyrToLat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/php',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'CyrToLat\\ACF' => __DIR__ . '/../..' . '/src/php/ACF.php',
        'CyrToLat\\AdminNotices' => __DIR__ . '/../..' . '/src/php/AdminNotices.php',
        'CyrToLat\\BackgroundProcesses\\ConversionProcess' => __DIR__ . '/../..' . '/src/php/BackgroundProcesses/ConversionProcess.php',
        'CyrToLat\\BackgroundProcesses\\PostConversionProcess' => __DIR__ . '/../..' . '/src/php/BackgroundProcesses/PostConversionProcess.php',
        'CyrToLat\\BackgroundProcesses\\TermConversionProcess' => __DIR__ . '/../..' . '/src/php/BackgroundProcesses/TermConversionProcess.php',
        'CyrToLat\\ConversionTables' => __DIR__ . '/../..' . '/src/php/ConversionTables.php',
        'CyrToLat\\Converter' => __DIR__ . '/../..' . '/src/php/Converter.php',
        'CyrToLat\\ErrorHandler' => __DIR__ . '/../..' . '/src/php/ErrorHandler.php',
        'CyrToLat\\Main' => __DIR__ . '/../..' . '/src/php/Main.php',
        'CyrToLat\\Request' => __DIR__ . '/../..' . '/src/php/Request.php',
        'CyrToLat\\Requirements' => __DIR__ . '/../..' . '/src/php/Requirements.php',
        'CyrToLat\\Settings\\Abstracts\\SettingsBase' => __DIR__ . '/../..' . '/src/php/Settings/Abstracts/SettingsBase.php',
        'CyrToLat\\Settings\\Abstracts\\SettingsInterface' => __DIR__ . '/../..' . '/src/php/Settings/Abstracts/SettingsInterface.php',
        'CyrToLat\\Settings\\Converter' => __DIR__ . '/../..' . '/src/php/Settings/Converter.php',
        'CyrToLat\\Settings\\PluginSettingsBase' => __DIR__ . '/../..' . '/src/php/Settings/PluginSettingsBase.php',
        'CyrToLat\\Settings\\Settings' => __DIR__ . '/../..' . '/src/php/Settings/Settings.php',
        'CyrToLat\\Settings\\SystemInfo' => __DIR__ . '/../..' . '/src/php/Settings/SystemInfo.php',
        'CyrToLat\\Settings\\Tables' => __DIR__ . '/../..' . '/src/php/Settings/Tables.php',
        'CyrToLat\\Symfony\\Polyfill\\Mbstring\\Mbstring' => __DIR__ . '/../..' . '/libs/polyfill-mbstring/Mbstring.php',
        'CyrToLat\\WPCli' => __DIR__ . '/../..' . '/src/php/WPCli.php',
        'CyrToLat\\WP_Background_Processing\\WP_Async_Request' => __DIR__ . '/../..' . '/libs/wp-background-processing/wp-async-request.php',
        'CyrToLat\\WP_Background_Processing\\WP_Background_Process' => __DIR__ . '/../..' . '/libs/wp-background-processing/wp-background-process.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcb805c9fadce0583c03c04522eed9724::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcb805c9fadce0583c03c04522eed9724::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcb805c9fadce0583c03c04522eed9724::$classMap;

        }, null, ClassLoader::class);
    }
}
