<?php declare(strict_types=1);

namespace App;

use App\Exceptions\EnvVariableNotFoundException;
use App\KanbanBoard\Interfaces\ArraybleInterface;
use App\Support\Env;
use Tightenco\Collect\Support\Collection;

class Utilities
{
    /**
     * Deny from instantiating. Use only static methods.
     */
	private function __construct() {
	}

    /**
     * Gets variable from .env file or $_ENV array
     *
     * @param string $name Variable name
     * @param string|null $default Variable default value
     * @return string
     * @throws EnvVariableNotFoundException
     */
	public static function env(string $name, string $default = NULL): string {

	    $value = Env::get($name, $default);

		if ( empty($value) && $default === NULL ) {
            throw new EnvVariableNotFoundException("Environment variable {$name} not found or has no value");
        }

		return $value;
	}

    /**
     * Checks if key exists in an array
     *
     * @param array $array
     * @param string $key
     * @return bool
     */
	public static function hasValue(array $array, string $key) {
		return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
	}

    /**
     * Dumps the variable with pre-formatting
     *
     * @param $data
     */
	public static function dump($data): void {
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}

    /**
     * Generates a random string with given length
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
	public static function randomString(int $length = 40): string {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Converts Collection of items into array of items.
     *
     * @param Collection $collection
     * @return array
     * @throws \RuntimeException
     */
    public static function mapToView(Collection $collection): array {
	    $mapped = $collection->map(function ($item) {
	        if ( !$item instanceof ArraybleInterface) {
	            throw new \RuntimeException(get_class($item) . " must implements App\KanbanBoard\Interfaces\ArraybleInterface");
            }
	        return $item->toArray();
        });

	    return $mapped->all();
    }
}