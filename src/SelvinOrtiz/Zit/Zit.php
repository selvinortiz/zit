<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Selvin Ortiz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace SelvinOrtiz\Zit;

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
		if ( is_null( static::$instance ) ) {
			$callingClassName	= get_called_class();
			static::$instance	= new $callingClassName;
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
	 * @return	<obj>						The service instance
	 */
	public function stash( $id, $serviceInstance )
	{
		if ( is_object( $serviceInstance ) ) {
			$this->services[ $id ] = $serviceInstance;
		}

		return $this;
	}

	/**
	 * @extend()
	 * Must bind the callable function and execute it by its id
	 *
	 * @param	<str>	$id					The callable function id
	 * @param	<obj>	$callable			The callable function
	 * @return	<mix>						The returned value from callable
	 */
	public function extend( $id, \Closure $callable )
	{
		if ( is_callable( $callable ) || method_exists( $callable, '__invoke') ) {
			$this->callables[ $id ] = $callable;
		}

		return $this;
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

	protected function execute( $callable, $args=array() )
	{
		if ( is_array( $args ) && count( $args ) ) {
			$count = count( $args );
			switch( $count ) {
				case 1:
					return $callable->__invoke( $args[ 0 ], $this );
				break;
				case 2:
					return $callable->__invoke( $args[ 0 ], $args[ 1 ], $this );
				break;
				case 3:
					return $callable->__invoke( $args[ 0 ], $args[ 1 ], $args[ 2 ], $this );
				break;
				case 4:
					return $callable->__invoke( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $this );
				break;
				case 5:
					return $callable->__invoke( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $this );
				break;
				default:
					return $callable->__invoke( $args, $this );
				break;
			}
		}

		return $callable->__invoke( $this );
	}
}
