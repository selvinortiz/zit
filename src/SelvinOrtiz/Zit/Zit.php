<?php
namespace SelvinOrtiz\Zit;

/**
 * @=SelvinOrtiz\Zit
 *
 * Tiny dependency management library for PHP 5.3
 *
 * @author		Selvin Ortiz <selvin@selvinortiz.com>
 * @package		Zit
 * @version		0.4.0
 * @category	DI, IoC (PHP)
 * @copyright	2013 Selvin Ortiz
 */

class Zit implements IZit
{
	protected static $instance;

	protected $services		= array();
	protected $callables	= array();

	protected function __construct() 	{}
	protected function __clone()		{}

	/**
	 * @getInstance()
	 * Fetch|Make the instance of Zit|extending class called statically
	 *
	 * @return	<obj>
	 */
	public static function getInstance()
	{
		if ( null === static::$instance ) {
			$calledClass		= get_called_class();
			static::$instance	= new $calledClass;
		}

		return static::$instance;
	}

	/**
	 * @bind()
	 * Must bind the service generator and resolve it by its id
	 *
	 * @param	<str>	$id					The service generator id
	 * @param	<obj>	$serviceGenerator	The service generator closure/function
	 * @return	<obj>						The generated (static) service instance
	 */
	public function bind( $id, \Closure $serviceGenerator )
	{
		$this->callables[ (string) $id ] = function( $zit ) use( $serviceGenerator )
		{
			static $object;

			if ( null === $object ) {
				$object = $serviceGenerator( $zit );
			}

			return $object;
		};
	}

	/**
	 * @stash()
	 * Stash away a service instance and resolve it by its id
	 *
	 * @param	<str>	$id					The service instance id
	 * @param	<obj>	$serviceInstance	The service instance
	 */
	public function stash( $id, $serviceInstance )
	{
		if ( is_object( $serviceInstance ) ) {
			$this->services[ $id ] = $serviceInstance;
		}
	}

	/**
	 * @extend()
	 * Must bind the callable function and execute it by its id
	 *
	 * @todo :	Remove on Zit 0.5.0
	 * @param	<str>	$id					The callable function id
	 * @param	<obj>	$callable			The callable function
	 */
	public function extend( $id, \Closure $callable )
	{
		if ( is_callable( $callable ) || method_exists( $callable, '__invoke') ) {
			$this->callables[ $id ] = $callable;
		}
	}

	/**
	 * @helper()
	 * Must bind the callable function and execute it by its id
	 *
	 * @todo  :	Replace extend() on Zit 0.5.0
	 * @param	<str>	$id					The callable function id
	 * @param	<obj>	$callable			The callable function
	 */
	public function helper( $id, \Closure $callable )
	{
		if ( is_callable( $callable ) || method_exists( $callable, '__invoke') ) {
			$this->callables[ $id ] = $callable;
		}
	}

	public function get( $id, $args=array() )
	{
		if ( array_key_exists( $id, $this->services ) ) {
			return $this->services[ $id ];
		}

		if ( array_key_exists( $id, $this->callables ) ) {
			$callable = $this->callables[ $id ];
			return $this->execute( $callable, $args );
		}

		throw new \Exception( "The dependency with id of {$id} is missing." );
	}

	public function __call( $id, $args=array() )
	{
		return $this->get( $id, $args );
	}

	public static function __callStatic( $id, $args=array() )
	{
		return static::getInstance()->get( $id, $args );
	}

	protected function execute( $callable, $args=array() )
	{
		if ( is_array( $args ) && count( $args ) ) {
			$count = count( $args );
			switch( $count ) {
				case 1:
					return $callable->__invoke( $this, $args[ 0 ] );
				break;
				case 2:
					return $callable->__invoke( $this, $args[ 0 ], $args[ 1 ] );
				break;
				case 3:
					return $callable->__invoke( $this, $args[ 0 ], $args[ 1 ], $args[ 2 ] );
				break;
				case 4:
					return $callable->__invoke( $this, $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
				break;
				case 5:
					return $callable->__invoke( $this, $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
				break;
				default:
					return $callable->__invoke( $this, $args );
				break;
			}
		}

		return $callable->__invoke( $this );
	}
}
