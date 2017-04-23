<?php

namespace Aimeos\Controller\ExtJS\Locale;


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
		$this->object = new \Aimeos\Controller\ExtJS\Locale\Standard( \TestHelperExtjs::getContext() );
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


	public function testAbstractInit()
	{
		$expected = array( 'success' => true );
		$actual = $this->object->init( new \stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractFinish()
	{
		$expected = array( 'success' => true );
		$actual = $this->object->finish( new \stdClass() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					(object) array( '==' => (object) array( 'locale.siteid' => \TestHelperExtjs::getContext()->getLocale()->getSiteId() ) ),
					(object) array( '==' => (object) array( 'locale.currencyid' => 'EUR' ) ),
				),
			),
			'sort' => 'locale.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'EUR', $result['items'][0]->{'locale.currencyid'} );
	}


	public function testSaveDeleteItem()
	{

		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'locale.siteid' => \TestHelperExtjs::getContext()->getLocale()->getSiteId(),
				'locale.currencyid' => 'CHF',
				'locale.languageid' => 'de',
				'locale.status' => 0,
				'locale.position' => 1,
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.currencyid' => 'CHF' ) ) ) ) );

		$saved = $this->object->saveItems( $saveParam );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'locale.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'locale.id'} );
		$this->assertEquals( $saved['items']->{'locale.id'}, $searched['items'][0]->{'locale.id'} );
		$this->assertEquals( $saved['items']->{'locale.languageid'}, $searched['items'][0]->{'locale.languageid'} );
		$this->assertEquals( $saved['items']->{'locale.currencyid'}, $searched['items'][0]->{'locale.currencyid'} );
		$this->assertEquals( $saved['items']->{'locale.status'}, $searched['items'][0]->{'locale.status'} );
		$this->assertEquals( $saved['items']->{'locale.position'}, $searched['items'][0]->{'locale.position'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testSaveCheckParamsAbstractException()
	{
		$saveParam = (object) [];
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->saveItems( $saveParam );
	}


	public function testAbstractSetLocaleException()
	{
		$saveParam = (object) array(
			'site' => 'badSite',
			'items' => (object) [],
		);
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->saveItems( $saveParam );
	}

}
