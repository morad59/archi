<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0d568b7c9be600159e3c3d3c591b0aba
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0d568b7c9be600159e3c3d3c591b0aba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0d568b7c9be600159e3c3d3c591b0aba::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0d568b7c9be600159e3c3d3c591b0aba::$classMap;

        }, null, ClassLoader::class);
    }
}
