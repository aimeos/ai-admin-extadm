<?php

namespace Aimeos\Controller\ExtJS\Attribute\Export\Text;


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
		$this->object = new \Aimeos\Controller\ExtJS\Attribute\Export\Text\Standard( $this->context );
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
		$manager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$ids = [];
		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new \stdClass();
		$params->lang = array( 'de' );
		$params->items = $ids;
		$params->site = 'unittest';

		$result = $this->object->exportFile( $params );

		$this->assertTrue( array_key_exists( 'file', $result ) );

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

		$this->assertEquals( 'Language ID', $lines[0][0] );
		$this->assertEquals( 'Text', $lines[0][6] );

		$this->assertEquals( 'de', $lines[8][0] );
		$this->assertEquals( 'color', $lines[8][1] );
		$this->assertEquals( 'red', $lines[8][2] );
		$this->assertEquals( 'default', $lines[8][3] );
		$this->assertEquals( 'name', $lines[8][4] );
		$this->assertEquals( '', $lines[8][6] );

		$this->assertEquals( '', $lines[163][0] );
		$this->assertEquals( 'width', $lines[163][1] );
		$this->assertEquals( '30', $lines[163][2] );
		$this->assertEquals( 'default', $lines[163][3] );
		$this->assertEquals( 'name', $lines[163][4] );
		$this->assertEquals( '30', $lines[163][6] );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->object->getServiceDescription();
		$expected = array(
			'Attribute_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "array", "name" => "lang", "optional" => true ),
				),
				"returns" => "",
			),
		);

		$this->assertEquals( $expected, $actual );
	}

}
