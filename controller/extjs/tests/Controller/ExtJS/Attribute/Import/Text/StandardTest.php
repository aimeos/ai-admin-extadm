<?php

namespace Aimeos\Controller\ExtJS\Attribute\Import\Text;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $testdir;
	private $testfile;
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

		$this->testdir = './tmp';
		$this->testfile = $this->testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->testdir ) && mkdir( $this->testdir, 0775, true ) === false ) {
			throw new \RuntimeException( sprintf( 'Unable to create missing upload directory "%1$s"', $this->testdir ) );
		}

		$this->object = new \Aimeos\Controller\ExtJS\Attribute\Import\Text\Standard( $this->context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		if( file_exists( $this->testfile ) ) {
			unlink( $this->testfile );
		}

		$this->object = null;
	}


	public function testGetServiceDescription()
	{
		$desc = $this->object->getServiceDescription();
		$this->assertInternalType( 'array', $desc );
		$this->assertEquals( 2, count( $desc['Attribute_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Attribute_Import_Text.importFile'] ) );
	}


	public function testImportFromCSVFile()
	{
		$data = [];
		$data[] = '"Language ID","Type","Code","List type","Text type","Text ID","Text"' . "\n";
		$data[] = '"en","color","white","default","name","","unittest: white"' . "\n";
		$data[] = '"en","color","blue","default","name","","unittest: blue"' . "\n";
		$data[] = '"en","color","red","default","name","","unittest: red"' . "\n";
		$data[] = '"en","size","l","default","name","","unittest: l"' . "\n";
		$data[] = '"en","size","xl","default","name","","unittest: xl"' . "\n";
		$data[] = '"en","size","xxl","default","name","","unittest: xxl"' . "\n";
		$data[] = ' ';

		$ds = DIRECTORY_SEPARATOR;
		$csv = 'en-attribute-test.csv';
		$filename = PATH_TESTS . $ds . 'tmp' . $ds . 'attribute-import.zip';

		if( file_put_contents( PATH_TESTS . $ds . 'tmp' . $ds . $csv, implode( '', $data ) ) === false ) {
			throw new \RuntimeException( sprintf( 'Unable to write test file "%1$s"', $csv ) );
		}

		$zip = new \ZipArchive();
		$zip->open( $filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );
		$zip->addFile( PATH_TESTS . $ds . 'tmp' . $ds . $csv, $csv );
		$zip->close();

		if( unlink( PATH_TESTS . $ds . 'tmp' . $ds . $csv ) === false ) {
			throw new \RuntimeException( 'Unable to remove export file' );
		}

		$params = new \stdClass();
		$params->site = $this->context->getLocale()->getSite()->getCode();
		$params->items = basename( $filename );

		$this->object->importFile( $params );

		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->context );
		$criteria = $textManager->createSearch();

		$expr = [];
		$expr[] = $criteria->compare( '==', 'text.domain', 'attribute' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'unittest:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = [];
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );
		$listManager = $attributeManager->getSubManager( 'lists' );
		$criteria = $listManager->createSearch();

		$expr = [];
		$expr[] = $criteria->compare( '==', 'attribute.lists.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'attribute.lists.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}

		foreach( $textItems as $item ) {
			$this->assertEquals( 'unittest:', substr( $item->getContent(), 0, 9 ) );
		}

		$this->assertEquals( 6, count( $textItems ) );
		$this->assertEquals( 6, count( $listItems ) );

		if( file_exists( $filename ) !== false ) {
			throw new \RuntimeException( 'Import file was not removed' );
		}
	}


	public function testUploadFile()
	{
		$helper = $this->getMockBuilder( '\Aimeos\MW\View\Helper\Request\Standard' )
			->setMethods( array( 'getUploadedFiles' ) )
			->disableOriginalConstructor()
			->getMock();

		$view = new \Aimeos\MW\View\Standard();
		$view->addHelper( 'request', $helper );
		$this->context->setView( $view );

		$object = $this->getMockBuilder( '\Aimeos\Controller\ExtJS\Attribute\Import\Text\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'storeFile' ) )
			->getMock();
		$object->expects( $this->once() )->method( 'storeFile' )->will( $this->returnValue( 'file.txt' ) );

		$params = new \stdClass();
		$params->items = 'file.txt';
		$params->site = $this->context->getLocale()->getSite()->getCode();

		$object->uploadFile( $params );


		$jobController = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->context );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'job.parameter' => 'file.txt' ) ) ) ),
		);

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );

		$deleteParams = (object) array(
			'site' => 'unittest',
			'items' => $result['items'][0]->{'job.id'},
		);

		$jobController->deleteItems( $deleteParams );

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testUploadFileExeptionNoFiles()
	{
		$helper = $this->getMockBuilder( '\Aimeos\MW\View\Helper\Request\Standard' )
			->setMethods( array( 'getUploadedFiles' ) )
			->disableOriginalConstructor()
			->getMock();
		$helper->expects( $this->once() )->method( 'getUploadedFiles' )->will( $this->returnValue( [] ) );

		$view = new \Aimeos\MW\View\Standard();
		$view->addHelper( 'request', $helper );

		$this->context->setView( $view );


		$params = new \stdClass();
		$params->items = basename( $this->testfile );
		$params->site = 'unittest';

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->uploadFile( $params );
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->object->getItemSchema();
		$expected = array(
			'name' => 'Attribute_Import_Text',
			'properties' => [],
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->object->getSearchSchema();
		$expected = array(
			'criteria' => []
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractSetLocaleException()
	{
		$params = (object) array(
			'site' => 'badSite',
			'items' => (object) [],
		);
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->uploadFile( $params );
	}


	public function testAbstractCheckParamsException()
	{
		$params = (object) [];
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		$this->object->uploadFile( $params );
	}


	public function testAbstractStoreFile()
	{
		$stream = $this->getMockBuilder( '\Psr\Http\Message\StreamInterface' )->getMock();
		$file = $this->getMockBuilder( '\Psr\Http\Message\UploadedFileInterface' )->getMock();
		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();

		$fsm = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Manager\Standard' )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Standard' )
			->setMethods( array( 'writes' ) )
			->disableOriginalConstructor()
			->getMock();

		$file->expects( $this->once() )->method( 'getError' )->will( $this->returnValue( 0 ) );
		$file->expects( $this->once() )->method( 'getStream' )->will( $this->returnValue( $stream ) );
		$request->expects( $this->once() )->method( 'getUploadedFiles' )->will( $this->returnValue( array( $file ) ) );
		$fsm->expects( $this->once() )->method( 'get' )->will( $this->returnValue( $fs ) );
		$fs->expects( $this->once() )->method( 'writes' );

		$this->context->setFilesystemManager( $fsm );


		$class = new \ReflectionClass( '\Aimeos\Controller\ExtJS\Attribute\Import\Text\Standard' );
		$method = $class->getMethod( 'storeFile' );
		$method->setAccessible( true );

		$method->invokeArgs( $this->object, array( $request ) );
	}
}
