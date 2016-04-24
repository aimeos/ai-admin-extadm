<?php

namespace Aimeos\Controller\ExtJS\Locale\Language;


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
		$this->object = new \Aimeos\Controller\ExtJS\Locale\Language\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.language.code' => 'de' ) ) ) ),
			'sort' => 'locale.language.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'de', $result['items'][0]->{'locale.language.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'items' => (object) array(
				'locale.language.code' => 'xx',
				'locale.language.label' => 'XX',
				'locale.language.status' => 1
			),
		);

		$searchParams = (object) array(
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => array( 'locale.language.code' => 'xx' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'items' => $saved['items']->{'locale.language.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'locale.language.id'} );
		$this->assertEquals( $saved['items']->{'locale.language.id'}, $searched['items'][0]->{'locale.language.id'} );
		$this->assertEquals( $saved['items']->{'locale.language.code'}, $searched['items'][0]->{'locale.language.code'} );
		$this->assertEquals( $saved['items']->{'locale.language.label'}, $searched['items'][0]->{'locale.language.label'} );
		$this->assertEquals( $saved['items']->{'locale.language.status'}, $searched['items'][0]->{'locale.language.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Locale_Language.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.saveItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.searchItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
		);

		$actual = $this->object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}

}
