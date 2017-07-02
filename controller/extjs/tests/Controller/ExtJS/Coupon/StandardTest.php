<?php

namespace Aimeos\Controller\ExtJS\Coupon;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
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
		$this->object = new \Aimeos\Controller\ExtJS\Coupon\Standard( \TestHelperExtjs::getContext() );
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
					0 => array( '~=' => (object) array( 'coupon.provider' => 'FixedRebate' ) ),
					1 => array( '==' => (object) array( 'coupon.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'coupon.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'Unit test fixed rebate', $result['items'][0]->{'coupon.label'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'coupon.provider' => 'NewBestsellerProvider',
				'coupon.label' => 'Bestseller items sell cheaper',
				'coupon.config' => array( 'rebate' => '5%' ),
				'coupon.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'coupon.provider' => 'NewBestsellerProvider' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => array( $saved['items']->{'coupon.id'}) );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'coupon.id'} );
		$this->assertEquals( $saved['items']->{'coupon.id'}, $searched['items'][0]->{'coupon.id'} );
		$this->assertEquals( $saved['items']->{'coupon.label'}, $searched['items'][0]->{'coupon.label'} );
		$this->assertEquals( $saved['items']->{'coupon.provider'}, $searched['items'][0]->{'coupon.provider'} );
		$this->assertEquals( $saved['items']->{'coupon.config'}, $searched['items'][0]->{'coupon.config'} );
		$this->assertEquals( $saved['items']->{'coupon.status'}, $searched['items'][0]->{'coupon.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
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
}