<?php

namespace App\Support;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\RepositoryBuilder;

class Env
{
    /**
     * Env constructor.
     */
    private function __construct()
    {
    }

    /**
     * The environment repository instance.
     *
     * @var \Dotenv\Repository\RepositoryInterface|null
     */
    protected static $repository;

    /**
     * Get the environment repository instance.
     *
     * @return \Dotenv\Repository\RepositoryInterface
     */
    public static function getRepository()
    {
        if (static::$repository === null) {
            $adapters = [new EnvConstAdapter, new ServerConstAdapter];
            static::$repository = RepositoryBuilder::create()
                ->withReaders($adapters)
                ->withWriters($adapters)
                ->immutable()
                ->make();


        }

        return static::$repository;
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $value = static::getRepository()->get($key);

        if ( !$value ) {
            return  $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }

        if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
            return $matches[2];
        }
    }
}
