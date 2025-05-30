<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInita91407656d66b1ad0a0c99a5567d747f
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInita91407656d66b1ad0a0c99a5567d747f', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInita91407656d66b1ad0a0c99a5567d747f', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInita91407656d66b1ad0a0c99a5567d747f::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
