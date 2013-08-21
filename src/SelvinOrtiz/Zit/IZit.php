<?php
namespace SelvinOrtiz\Zit;

interface IZit
{
	/**
	 * Must bind the service generator and resolve it by its id
	 *
	 * @param	<str>	$id					The service generator id
	 * @param	<obj>	$serviceGenerator	The service generator closure/function
	 * @return	<obj>						The generated (static) service instance
	 */
	public function bind( $id, \Closure $serviceGenerator );

	/**
	 * Stash away a service instance and resolve it by its id
	 *
	 * @param	<str>	$id					The service instance id
	 * @param	<obj>	$serviceInstance	The service instance
	 * @return	<obj>						The service instance
	 */
	public function stash( $id, $serviceInstance );

	/**
	 * Must bind the callable function and execute it by its id
	 *
	 * @param	<str>	$id					The callable function id
	 * @param	<obj>	$callable			The callable function
	 * @return	<mix>						The returned value from callable
	 */
	public function extend( $id, \Closure $callable );
}
