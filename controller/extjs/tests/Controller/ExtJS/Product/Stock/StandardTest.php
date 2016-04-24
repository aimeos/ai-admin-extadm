<?php

namespace Aimeos\Controller\ExtJS\Product\Stock;


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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Stock\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.stocklevel' => 1000 ) ) ) ),
			'sort' => 'product.stock.stocklevel',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 1000, $result['items'][0]->{'product.stock.stocklevel'} );
	}


	public function testSaveDeleteItem()
	{
		$ctx = \TestHelperExtjs::getContext();

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $ctx );
		$warehouseManager = $productManager->getSubManager( 'stock' )->getSubManager( 'warehouse' );

		$search = $warehouseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.code', 'default' ) );
		$search->setSlice( 0, 1 );
		$items = $warehouseManager->searchItems( $search );

		if( ( $warehouseItem = reset( $items ) ) === false ) {
			throw new \Exception( 'No item found' );
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '~=', 'product.label', 'Cheapest' ) );
		$items = $productManager->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new \Exception( 'No item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.stock.parentid' => $productItem->getId(),
				'product.stock.warehouseid' => $warehouseItem->getId(),
				'product.stock.stocklevel' => 999,
				'product.stock.dateback' => '2000-01-01 00:00:01',
			),
		);
		$saved = $this->object->saveItems( $saveParams );

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.dateback' => '2000-01-01 00:00:01' ) ) ) )
		);
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.stock.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.stock.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.id'}, $searched['items'][0]->{'product.stock.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.parentid'}, $searched['items'][0]->{'product.stock.parentid'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouseid'}, $searched['items'][0]->{'product.stock.warehouseid'} );
		$this->assertEquals( $saved['items']->{'product.stock.stocklevel'}, $searched['items'][0]->{'product.stock.stocklevel'} );
		$this->assertEquals( $saved['items']->{'product.stock.dateback'}, $searched['items'][0]->{'product.stock.dateback'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
