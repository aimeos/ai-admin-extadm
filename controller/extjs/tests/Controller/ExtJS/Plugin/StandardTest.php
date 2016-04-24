<?php

namespace Aimeos\Controller\ExtJS\Plugin;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Plugin\Standard( \TestHelperExtjs::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'plugin.provider' => 'Shipping' ) ) ) ),
			'sort' => 'plugin.provider',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		if( ( $plugin = reset( $result ) ) === false ) {
			throw new \Exception( 'No plugin found' );
		}

		$this->assertEquals( 1, count( $plugin ) );
		$this->assertEquals( reset( $plugin )->{'plugin.provider'}, 'Shipping,Example' );
	}


	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$typeManager = $manager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'plugin.type.code', 'order' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \Exception( 'No plugin type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'plugin.status' => 1,
				'plugin.position' => 2,
				'plugin.provider' => 'test provider',
				'plugin.config' => array( 'url' => 'www.url.de' ),
				'plugin.typeid' => $type->getId(),
				'plugin.label' => 'test plugin',
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'plugin.provider' => 'test provider' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'plugin.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'plugin.id'} );
		$this->assertEquals( $saved['items']->{'plugin.id'}, $searched['items'][0]->{'plugin.id'} );
		$this->assertEquals( $saved['items']->{'plugin.status'}, $searched['items'][0]->{'plugin.status'} );
		$this->assertEquals( $saved['items']->{'plugin.position'}, $searched['items'][0]->{'plugin.position'} );
		$this->assertEquals( $saved['items']->{'plugin.provider'}, $searched['items'][0]->{'plugin.provider'} );
		$this->assertEquals( $saved['items']->{'plugin.config'}, $searched['items'][0]->{'plugin.config'} );
		$this->assertEquals( $saved['items']->{'plugin.typeid'}, $searched['items'][0]->{'plugin.typeid'} );
		$this->assertEquals( $saved['items']->{'plugin.label'}, $searched['items'][0]->{'plugin.label'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
