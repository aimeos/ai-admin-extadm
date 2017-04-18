<?php

namespace Aimeos\Controller\ExtJS\Product\Export\Text;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperExtjs::getContext();
		$this->object = new \Aimeos\Controller\ExtJS\Product\Export\Text\Standard( $this->context );
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


	public function testExportCSVFile()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$criteria = $productManager->createSearch();

		$expr = [];
		$expr[] = $criteria->compare( '==', 'product.code', 'CNE' );
		$criteria->setConditions( $criteria->compare( '==', 'product.code', 'CNE' ) );

		$searchResult = $productManager->searchItems( $criteria );

		if( ( $productItem = reset( $searchResult ) ) === false ) {
			throw new \RuntimeException( 'No item with product code CNE found' );
		}

		$params = new \stdClass();
		$params->site = $this->context->getLocale()->getSite()->getCode();
		$params->items = $productItem->getId();
		$params->lang = 'de';

		$result = $this->object->exportFile( $params );
		$file = substr( $result['file'], 9, -14 );

		$this->assertTrue( file_exists( $file ) );

		$zip = new \ZipArchive();
		$zip->open( $file );

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
		if( !is_dir( $testdir ) && mkdir( $testdir, 0755, true ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
		}

		$zip->extractTo( $testdir );
		$zip->close();

		if( unlink( $file ) === false ) {
			throw new \RuntimeException( 'Unable to remove export file' );
		}

		$deCSV = $testdir . DIRECTORY_SEPARATOR . 'de.csv';

		$this->assertTrue( file_exists( $deCSV ) );
		$fh = fopen( $deCSV, 'r' );
		$lines = [];

		while( ( $data = fgetcsv( $fh ) ) != false ) {
			$lines[] = $data;
		}

		fclose( $fh );

		if( unlink( $deCSV ) === false ) {
			throw new \RuntimeException( 'Unable to remove export file' );
		}

		if( rmdir( $testdir ) === false ) {
			throw new \RuntimeException( 'Unable to remove test export directory' );
		}

		$this->assertEquals( $lines[0][0], 'Language ID' );
		$this->assertEquals( $lines[0][1], 'Product type' );
		$this->assertEquals( $lines[0][2], 'Product code' );
		$this->assertEquals( $lines[0][3], 'List type' );
		$this->assertEquals( $lines[0][4], 'Text type' );
		$this->assertEquals( $lines[0][5], 'Text ID' );
		$this->assertEquals( $lines[0][6], 'Text' );

		$this->assertEquals( 'de', $lines[2][0] );
		$this->assertEquals( 'default', $lines[2][1] );
		$this->assertEquals( 'CNE', $lines[2][2] );
		$this->assertEquals( 'unittype13', $lines[2][3] );
		$this->assertEquals( 'meta-description', $lines[2][4] );
		$this->assertEquals( 'Expresso', $lines[2][6] );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Product_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "array", "name" => "lang", "optional" => true ),
				),
				"returns" => "",
			),
		);

		$actual = $this->object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}
}