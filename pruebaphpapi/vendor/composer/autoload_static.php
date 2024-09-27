<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3636e8a24f4d67928aff04492abfb97c
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'Api\\Hardware\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Api\\Hardware\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3636e8a24f4d67928aff04492abfb97c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3636e8a24f4d67928aff04492abfb97c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3636e8a24f4d67928aff04492abfb97c::$classMap;

        }, null, ClassLoader::class);
    }
}