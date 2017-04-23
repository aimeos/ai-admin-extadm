<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\ExtJS\Admin\Cache;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Admin\Cache\Standard( \TestHelperExtjs::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'cache.id' => 'unittest' ) ) ) ),
			'sort' => 'cache.id',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 'unittest', $result['items'][0]->{'cache.id'} );
		$this->assertEquals( 'unit test value', $result['items'][0]->{'cache.value'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'cache.id' => 'unittest:extjs',
				'cache.value' => 'unittest extjs value',
				'cache.expire' => '2000-01-01 00:00:00',
				'cache.tags' => array( 'tag:1' ),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'cache.id' => 'unittest:extjs' ) ) ) ),
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'cache.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertEquals( $saved['items']->{'cache.id'}, $searched['items'][0]->{'cache.id'});
		$this->assertEquals( $saved['items']->{'cache.value'}, $searched['items'][0]->{'cache.value'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testClear()
	{
		$params = (object) array(
			'site' => 'unittest',
		);

		$this->object->clear( $params );
	}

}
