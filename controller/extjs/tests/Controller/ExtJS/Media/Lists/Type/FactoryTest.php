<?php

namespace Aimeos\Controller\ExtJS\Media\Lists\Type;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Media\Lists\Type\Factory::createController( \TestHelperExtjs::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Media\Lists\Type\Factory::createController( \TestHelperExtjs::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Media\Lists\Type\Factory::createController( \TestHelperExtjs::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Media\Lists\Type\Factory::createController( \TestHelperExtjs::getContext(), 'Factory' );
	}

}
