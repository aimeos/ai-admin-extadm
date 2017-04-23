<?php

namespace Aimeos\Controller\ExtJS\Product\Property\Type;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Product\Property\Type\Factory::createController( \TestHelperExtjs::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Iface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Product\Property\Type\Factory::createController( \TestHelperExtjs::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Product\Property\Type\Factory::createController( \TestHelperExtjs::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Product\Property\Type\Factory::createController( \TestHelperExtjs::getContext(), 'Factory' );
	}

}
