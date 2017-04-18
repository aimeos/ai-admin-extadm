<?php

namespace Aimeos\Controller\ExtJS\Attribute\Type;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit\Framework\TestCase
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
		$this->object = new \Aimeos\Controller\ExtJS\Attribute\Type\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => (object) array( '==' => (object) array( 'attribute.type.domain' => 'product' ) ),
					1 => (object) array( '==' => (object) array( 'attribute.type.code' => 'color' ) ),
				),
			),
			'sort' => 'attribute.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'color', $result['items'][0]->{'attribute.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'attribute.type.code' => 'test',
				'attribute.type.label' => 'testLabel',
				'attribute.type.domain' => 'attribute',
				'attribute.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.type.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'attribute.type.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.type.id'} );
		$this->assertEquals( $saved['items']->{'attribute.type.id'}, $searched['items'][0]->{'attribute.type.id'} );
		$this->assertEquals( $saved['items']->{'attribute.type.code'}, $searched['items'][0]->{'attribute.type.code'} );
		$this->assertEquals( $saved['items']->{'attribute.type.domain'}, $searched['items'][0]->{'attribute.type.domain'} );
		$this->assertEquals( $saved['items']->{'attribute.type.label'}, $searched['items'][0]->{'attribute.type.label'} );
		$this->assertEquals( $saved['items']->{'attribute.type.status'}, $searched['items'][0]->{'attribute.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
