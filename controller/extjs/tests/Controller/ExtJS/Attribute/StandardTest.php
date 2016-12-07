<?php

namespace Aimeos\Controller\ExtJS\Attribute;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		$this->object = new \Aimeos\Controller\ExtJS\Attribute\Standard( \TestHelperExtjs::getContext() );
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
					0 => array( '~=' => (object) array( 'attribute.code' => 'x' ) ),
					1 => array( '==' => (object) array( 'attribute.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'attribute.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'xl', $result['items'][0]->{'attribute.code'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperExtjs::getContext() );
		$typeManager = $manager->getSubManager( 'type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'attribute.typeid' => $type->getId(),
				'attribute.domain' => 'product',
				'attribute.code' => 'test',
				'attribute.label' => 'test label',
				'attribute.position' => 1,
				'attribute.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'attribute.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array( $saved['items']->{'attribute.id'}) );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.id'} );
		$this->assertEquals( $saved['items']->{'attribute.id'}, $searched['items'][0]->{'attribute.id'} );
		$this->assertEquals( $saved['items']->{'attribute.typeid'}, $searched['items'][0]->{'attribute.typeid'} );
		$this->assertEquals( $saved['items']->{'attribute.domain'}, $searched['items'][0]->{'attribute.domain'} );
		$this->assertEquals( $saved['items']->{'attribute.code'}, $searched['items'][0]->{'attribute.code'} );
		$this->assertEquals( $saved['items']->{'attribute.label'}, $searched['items'][0]->{'attribute.label'} );
		$this->assertEquals( $saved['items']->{'attribute.position'}, $searched['items'][0]->{'attribute.position'} );
		$this->assertEquals( $saved['items']->{'attribute.status'}, $searched['items'][0]->{'attribute.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->object->getItemSchema();
		$expected = array(
			'name' => 'Attribute',
			'properties' => array(
				'attribute.id' => array(
					'description' => 'Attribute ID',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.siteid' => array(
					'description' => 'Attribute site',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.typeid' => array(
					'description' => 'Attribute type',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.domain' => array(
					'description' => 'Attribute domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.code' => array(
					'description' => 'Attribute code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.position' => array(
					'description' => 'Attribute position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.label' => array(
					'description' => 'Attribute label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.status' => array(
					'description' => 'Attribute status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.ctime' => array(
					'description' => 'Attribute create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.mtime' => array(
					'description' => 'Attribute modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.editor' => array(
					'description' => 'Attribute editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type' => array(
					'description' => 'Attribute type code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.typename' => array(
					'description' => 'Attribute type name',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->object->getSearchSchema();
		$expected = array(
			'criteria' => array(
				'attribute.id' => array(
					'description' => 'Attribute ID',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.domain' => array(
					'description' => 'Attribute domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.code' => array(
					'description' => 'Attribute code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.position' => array(
					'description' => 'Attribute position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.label' => array(
					'description' => 'Attribute label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.status' => array(
					'description' => 'Attribute status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.ctime' => array(
					'description' => 'Attribute create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.mtime' => array(
					'description' => 'Attribute modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.editor' => array(
					'description' => 'Attribute editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.code' => array(
					'description' => 'Attribute type code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.domain' => array(
					'description' => 'Attribute type domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.label' => array(
					'description' => 'Attribute type label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.type.status' => array(
					'description' => 'Attribute type status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.type.ctime' => array(
					'description' => 'Attribute type create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.type.mtime' => array(
					'description' => 'Attribute type modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.type.editor' => array(
					'description' => 'Attribute type editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.domain' => array(
					'description' => 'Attribute list domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.refid' => array(
					'description' => 'Attribute list reference ID',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.datestart' => array(
					'description' => 'Attribute list start date',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.dateend' => array(
					'description' => 'Attribute list end date',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.config' => array(
					'description' => 'Attribute list config',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.position' => array(
					'description' => 'Attribute list position',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.lists.status' => array(
					'description' => 'Attribute list status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.lists.ctime' => array(
					'description' => 'Attribute list create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.mtime' => array(
					'description' => 'Attribute list modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.editor' => array(
					'description' => 'Attribute list editor',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.type.code' => array(
					'description' => 'Attribute list type code',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.type.domain' => array(
					'description' => 'Attribute list type domain',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.type.label' => array(
					'description' => 'Attribute list type label',
					'optional' => false,
					'type' => 'string',
				),
				'attribute.lists.type.status' => array(
					'description' => 'Attribute list type status',
					'optional' => false,
					'type' => 'integer',
				),
				'attribute.lists.type.ctime' => array(
					'description' => 'Attribute list type create date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.type.mtime' => array(
					'description' => 'Attribute list type modification date/time',
					'optional' => false,
					'type' => 'datetime',
				),
				'attribute.lists.type.editor' => array(
					'description' => 'Attribute list type editor',
					'optional' => false,
					'type' => 'string',
				),
			)
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractInitCriteriaException()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'attribute.code' => 'x' ) ) ) ),
			'sort' => 'attribute.code',
			'dir' => 'NO_SORTATION',
			'start' => 0,
			'limit' => 1,
		);

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->searchItems( $params );
	}


	public function testFinish()
	{
		$result = $this->object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}
