<?php
/**
 * This file is part of the php-resque package.
 *
 * (c) Michael Haynes <mike@mjphaynes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Resque;

use Predis;

/**
 * Resque redis class
 *
 * @author Michael Haynes <mike@mjphaynes.com>
 *
 * Interface defining a client able to execute commands against Redis.
 *
 * All the commands exposed by the client generally have the same signature as
 * described by the Redis documentation, but some of them offer an additional
 * and more friendly interface to ease programming which is described in the
 * following list of methods:
 *
 * @method int    del(array $keys)
 * @method string dump($key)
 * @method int    exists($key)
 * @method int    expire($key, $seconds)
 * @method int    expireat($key, $timestamp)
 * @method array  keys($pattern)
 * @method int    move($key, $db)
 * @method mixed  object($subcommand, $key)
 * @method int    persist($key)
 * @method int    pexpire($key, $milliseconds)
 * @method int    pexpireat($key, $timestamp)
 * @method int    pttl($key)
 * @method string randomkey()
 * @method mixed  rename($key, $target)
 * @method int    renamenx($key, $target)
 * @method array  scan($cursor, array $options = null)
 * @method array  sort($key, array $options = null)
 * @method int    ttl($key)
 * @method mixed  type($key)
 * @method int    append($key, $value)
 * @method int    bitcount($key, $start = null, $end = null)
 * @method int    bitop($operation, $destkey, $key)
 * @method int    decr($key)
 * @method int    decrby($key, $decrement)
 * @method string get($key)
 * @method int    getbit($key, $offset)
 * @method string getrange($key, $start, $end)
 * @method string getset($key, $value)
 * @method int    incr($key)
 * @method int    incrby($key, $increment)
 * @method string incrbyfloat($key, $increment)
 * @method array  mget(array $keys)
 * @method mixed  mset(array $dictionary)
 * @method int    msetnx(array $dictionary)
 * @method mixed  psetex($key, $milliseconds, $value)
 * @method mixed  set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
 * @method int    setbit($key, $offset, $value)
 * @method int    setex($key, $seconds, $value)
 * @method int    setnx($key, $value)
 * @method int    setrange($key, $offset, $value)
 * @method int    strlen($key)
 * @method int    hdel($key, array $fields)
 * @method int    hexists($key, $field)
 * @method string hget($key, $field)
 * @method array  hgetall($key)
 * @method int    hincrby($key, $field, $increment)
 * @method string hincrbyfloat($key, $field, $increment)
 * @method array  hkeys($key)
 * @method int    hlen($key)
 * @method array  hmget($key, array $fields)
 * @method mixed  hmset($key, array $dictionary)
 * @method array  hscan($key, $cursor, array $options = null)
 * @method int    hset($key, $field, $value)
 * @method int    hsetnx($key, $field, $value)
 * @method array  hvals($key)
 * @method array  blpop(array $keys, $timeout)
 * @method array  brpop(array $keys, $timeout)
 * @method array  brpoplpush($source, $destination, $timeout)
 * @method string lindex($key, $index)
 * @method int    linsert($key, $whence, $pivot, $value)
 * @method int    llen($key)
 * @method string lpop($key)
 * @method int    lpush($key, array $values)
 * @method int    lpushx($key, $value)
 * @method array  lrange($key, $start, $stop)
 * @method int    lrem($key, $count, $value)
 * @method mixed  lset($key, $index, $value)
 * @method mixed  ltrim($key, $start, $stop)
 * @method string rpop($key)
 * @method string rpoplpush($source, $destination)
 * @method int    rpush($key, array $values)
 * @method int    rpushx($key, $value)
 * @method int    sadd($key, array $members)
 * @method int    scard($key)
 * @method array  sdiff(array $keys)
 * @method int    sdiffstore($destination, array $keys)
 * @method array  sinter(array $keys)
 * @method int    sinterstore($destination, array $keys)
 * @method int    sismember($key, $member)
 * @method array  smembers($key)
 * @method int    smove($source, $destination, $member)
 * @method string spop($key)
 * @method string srandmember($key, $count = null)
 * @method int    srem($key, $member)
 * @method array  sscan($key, $cursor, array $options = null)
 * @method array  sunion(array $keys)
 * @method int    sunionstore($destination, array $keys)
 * @method int    zadd($key, array $membersAndScoresDictionary)
 * @method int    zcard($key)
 * @method string zcount($key, $min, $max)
 * @method string zincrby($key, $increment, $member)
 * @method int    zinterstore($destination, array $keys, array $options = null)
 * @method array  zrange($key, $start, $stop, array $options = null)
 * @method array  zrangebyscore($key, $min, $max, array $options = null)
 * @method int    zrank($key, $member)
 * @method int    zrem($key, $member)
 * @method int    zremrangebyrank($key, $start, $stop)
 * @method int    zremrangebyscore($key, $min, $max)
 * @method array  zrevrange($key, $start, $stop, array $options = null)
 * @method array  zrevrangebyscore($key, $min, $max, array $options = null)
 * @method int    zrevrank($key, $member)
 * @method int    zunionstore($destination, array $keys, array $options = null)
 * @method string zscore($key, $member)
 * @method array  zscan($key, $cursor, array $options = null)
 * @method array  zrangebylex($key, $start, $stop, array $options = null)
 * @method int    zremrangebylex($key, $min, $max)
 * @method int    zlexcount($key, $min, $max)
 * @method int    pfadd($key, array $elements)
 * @method mixed  pfmerge($destinationKey, array $sourceKeys)
 * @method int    pfcount(array $keys)
 * @method mixed  pubsub($subcommand, $argument)
 * @method int    publish($channel, $message)
 * @method mixed  discard()
 * @method array  exec()
 * @method mixed  multi()
 * @method mixed  unwatch()
 * @method mixed  watch($key)
 * @method mixed  eval($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed  evalsha($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed  script($subcommand, $argument = null)
 * @method mixed  auth($password)
 * @method string echo($message)
 * @method mixed  ping($message = null)
 * @method mixed  select($database)
 * @method mixed  bgrewriteaof()
 * @method mixed  bgsave()
 * @method mixed  client($subcommand, $argument = null)
 * @method mixed  config($subcommand, $argument = null)
 * @method int    dbsize()
 * @method mixed  flushall()
 * @method mixed  flushdb()
 * @method array  info($section = null)
 * @method int    lastsave()
 * @method mixed  save()
 * @method mixed  slaveof($host, $port)
 * @method mixed  slowlog($subcommand, $argument = null)
 * @method array  time()
 * @method array  command()
 */
class Redis {

	/**
	 * Default Redis connection scheme
	 */
	const DEFAULT_SCHEME = 'tcp';

	/**
	 * Default Redis connection host
	 */
	const DEFAULT_HOST = '127.0.0.1';

	/**
	 * Default Redis connection port
	 */
	const DEFAULT_PORT = 6379;

	/**
	 * Default Redis namespace
	 */
	const DEFAULT_NS = 'resque';

	/**
	* Default Redis AUTH password
	*/
	const DEFAULT_PASSWORD = null;

	/**
	 * @var array Default configuration
	 */
	protected static $config = array(
		'scheme'    => self::DEFAULT_SCHEME,
		'host'      => self::DEFAULT_HOST,
		'port'      => self::DEFAULT_PORT,
		'namespace' => self::DEFAULT_NS,
		'password'  => self::DEFAULT_PASSWORD
	);

	/**
	 * @var Redis Redis instance
	 */
	protected static $instance = null;

	/**
	 * Establish a Redis connection
	 *
	 * @return Redis
	 */
	public static function instance() {
		if (!static::$instance) {
			static::$instance = new static(static::$config);
		}

		return static::$instance;
	}

	/**
	 * Set the Redis config
	 *
	 * @param  array $config Array of configuration settings
	 */
	public static function setConfig(array $config) {
		static::$config = array_merge(static::$config, $config);
	}

	/**
	 * @var \Predis\Client  The Predis instance
	 */
	protected $redis;

	/**
	 * @var string  Redis namespace
	 */
	protected $namespace;

	/**
	 * @var array List of all commands in Redis that supply a key as their
	 *	first argument. Used to prefix keys with the Resque namespace.
	 */
	protected $keyCommands = array(
		'exists',
		'del',
		'type',
		'keys',
		'expire',
		'ttl',
		'move',
		'set',
		'setex',
		'get',
		'getset',
		'setnx',
		'incr',
		'incrby',
		'decr',
		'decrby',
		'rpush',
		'lpush',
		'llen',
		'lrange',
		'ltrim',
		'lindex',
		'lset',
		'lrem',
		'lpop',
		'blpop',
		'rpop',
		'sadd',
		'srem',
		'spop',
		'scard',
		'sismember',
		'smembers',
		'srandmember',
		'hdel',
		'hexists',
		'hget',
		'hgetall',
		'hincrby',
		'hincrbyfloat',
		'hkeys',
		'hlen',
		'hmget',
		'hmset',
		'hset',
		'hsetnx',
		'hvals',
		'zadd',
		'zrem',
		'zrange',
		'zrevrange',
		'zrangebyscore',
		'zrevrangebyscore',
		'zcard',
		'zscore',
		'zremrangebyscore',
		'sort',
		// sinterstore
		// sunion
		// sunionstore
		// sdiff
		// sdiffstore
		// sinter
		// smove
		// rename
		// rpoplpush
		// mget
		// msetnx
		// mset
		// renamenx
	);

	/**
	 * Establish a Redis connection.
	 *
	 * @param  array $config Array of configuration settings
	 * @return void
	 */
	public function __construct(array $config = array()) {

        $parameters = $config;
        $options = null;

        if (isset($config['options'])) {
	        unset($parameters['options']);
	        $options = $config['options'];
        }

		// setup password
		if (!empty($config['password'])) {
			$parameters['password'] = $config['password'];
		}

		// create Predis client
		$this->redis = new Predis\Client($parameters, $options);

		// setup namespace
		if (!empty($config['namespace'])) {
			$this->setNamespace($config['namespace']);
		} else {
			$this->setNamespace(self::DEFAULT_NS);
		}

		// Do this to test connection is working now rather than later
		$this->redis->connect();
	}

	/**
	 * Set Redis namespace
	 *
	 * @param string $namespace New namespace
	 */
	public function setNamespace($namespace) {
		if (substr($namespace, -1) !== ':') {
			$namespace .= ':';
		}

		$this->namespace = $namespace;
	}

	/**
	 * Get Redis namespace
	 *
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * Add Redis namespace to a string
	 *
	 * @param  string $string String to namespace
	 * @return string
	 */
	public function addNamespace($string) {
		if (is_array($string)) {
			foreach ($string as &$str) {
 				$str = $this->addNamespace($str);
			}

			return $string;
		}

		if (strpos($string, $this->namespace) !== 0) {
			$string = $this->namespace.$string;
		}

		return $string;
	}

	/**
	 * Remove Redis namespace from string
	 *
	 * @param  string $string String to de-namespace
	 * @return string
	 */
	public function removeNamespace($string) {
		$prefix = $this->namespace;

		if (substr($string, 0, strlen($prefix)) == $prefix) {
			$string = substr($string, strlen($prefix), strlen($string));
		}

		return $string;
	}

	/**
	 * Dynamically pass calls to the Predis.
	 *
	 * @param  string  $method     Method to call
	 * @param  array   $parameters Arguments to send to method
	 * @return mixed
	 */
	public function __call($method, $parameters) {
		if (in_array($method, $this->keyCommands)) {
			$parameters[0] = $this->addNamespace($parameters[0]);
		}

		// try {
			return call_user_func_array(array($this->redis, $method), $parameters);

		// } catch (\Exception $e) {
		// 	return false;
		// }
	}

}