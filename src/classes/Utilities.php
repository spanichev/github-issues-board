<?php
namespace App;

use App\Exceptions\EnvVariableNotFoundException;
use App\Support\Env;

class Utilities
{
	private function __construct() {
	}

	public static function env($name, $default = NULL) {

	    $value = Env::get($name, $default);

		if ( empty($value) && $default === NULL ) {
            throw new EnvVariableNotFoundException("Environment variable {$name} not found or has no value");
        }

		return $value;
	}

	public static function hasValue($array, $key) {
		return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
	}

	public static function dump($data) {
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
}