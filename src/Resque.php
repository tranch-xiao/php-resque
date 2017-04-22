<?php
/**
 * This file is part of the php-resque package.
 *
 * (c) Michael Haynes <mike@mjphaynes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Resque\Redis;

/**
 * Main Resque class
 *
 * @author Michael Haynes <mike@mjphaynes.com>
 */
class Resque extends \yii\base\Component {

	/**
	 * php-resque version
	 */
	const VERSION = '1.0.0';

	public $parameters = array(
        'scheme'    => Redis::DEFAULT_SCHEME,
        'host'      => Redis::DEFAULT_HOST,
        'port'      => Redis::DEFAULT_PORT,
        'password'  => Redis::DEFAULT_PASSWORD,
        'namespace' => Redis::DEFAULT_NS,
    );

	public $options = null;

	/**
	 * @var array Configuration settings array.
	 */
	protected static $config = array();

	/**
	 * @var \Resque\Queue The queue instance.
	 */
	protected static $queue = null;

	/**
	 * Create a queue instance.
	 *
	 * @return \Resque\Queue
	 */
	public static function queue() {
		if (!static::$queue) {
			static::$queue = new Resque\Queue();
		}

		return static::$queue;
	}

	public function init() {
	    Redis::setConfig(array_merge($this->parameters, array(
	        'options' => $this->options
        )));
    }

	/**
	 * Dynamically pass calls to the default connection.
	 *
	 * @param  string $method     The method to call
	 * @param  array  $parameters The parameters to pass
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters) {
		$callable = array(static::queue(), $method);

		return call_user_func_array($callable, $parameters);
	}

	/**
	 * Gets Resque stats
	 *
	 * @return array
	 */
	public static function stats() {
		return Redis::instance()->hgetall('stats');
	}

}