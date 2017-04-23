<?php

namespace Aimeos\Controller\ExtJS\Catalog\Export\Text;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Catalog\Export\Text\Factory::createController( \TestHelperExtjs::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Common\\Load\\Text\\Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Catalog\Export\Text\Factory::createController( \TestHelperExtjs::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Catalog\Export\Text\Factory::createController( \TestHelperExtjs::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->expectException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Catalog\Export\Text\Factory::createController( \TestHelperExtjs::getContext(), 'Factory' );
	}

}
