<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\ExtJS\Stock;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Stock\Standard( \TestHelperExtjs::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'stock.stocklevel' => 1000 ) ) ) ),
			'sort' => 'stock.stocklevel',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 1000, $result['items'][0]->{'stock.stocklevel'} );
	}


	public function testSaveDeleteItem()
	{
		$ctx = \TestHelperExtjs::getContext();

		$typeManager = \Aimeos\MShop\Factory::createManager( $ctx, 'stock/type' );
		$typeItem = $typeManager->findItem( 'default' );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'stock.productcode' => 'U:CF',
				'stock.typeid' => $typeItem->getId(),
				'stock.stocklevel' => 999,
				'stock.dateback' => '2000-01-01 00:00:01',
			),
		);
		$saved = $this->object->saveItems( $saveParams );

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'stock.dateback' => '2000-01-01 00:00:01' ) ) ) )
		);
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'stock.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'stock.id'} );
		$this->assertEquals( $saved['items']->{'stock.id'}, $searched['items'][0]->{'stock.id'} );
		$this->assertEquals( $saved['items']->{'stock.productcode'}, $searched['items'][0]->{'stock.productcode'} );
		$this->assertEquals( $saved['items']->{'stock.typeid'}, $searched['items'][0]->{'stock.typeid'} );
		$this->assertEquals( $saved['items']->{'stock.stocklevel'}, $searched['items'][0]->{'stock.stocklevel'} );
		$this->assertEquals( $saved['items']->{'stock.dateback'}, $searched['items'][0]->{'stock.dateback'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
