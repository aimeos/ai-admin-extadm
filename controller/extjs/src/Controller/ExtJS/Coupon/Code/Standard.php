<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Coupon\Code;


/**
 * ExtJs coupon code controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Iface
{
	private $manager = null;


	/**
	 * Initializes the coupon controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Coupon_Code' );
	}


	/**
	 * Uploads a file with coupon codes and meta information.
	 *
	 * @param \stdClass $params Object containing the properties
	 */
	public function uploadFile( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'parentid' ) );
		$this->setLocale( $params->site );

		if( ( $fileinfo = reset( $_FILES ) ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded' );
		}

		$config = $this->getContext()->getConfig();

		/** controller/extjs/coupon/code/standard/uploaddir
		 * Upload directory for text files that should be imported
		 *
		 * The upload directory must be an absolute path. Avoid a trailing slash
		 * at the end of the upload directory string!
		 *
		 * @param string Absolute path including a leading slash
		 * @since 2014.09
		 * @category Developer
		 */
		$dir = $config->get( 'controller/extjs/coupon/code/standard/uploaddir', 'uploads' );

		/** controller/extjs/coupon/code/standard/enablecheck
		 * Enables checking uploaded files if they are valid and not part of an attack
		 *
		 * This configuration option is for unit testing only! Please don't disable
		 * the checks for uploaded files in production environments as this
		 * would give attackers the possibility to infiltrate your installation!
		 *
		 * @param boolean True to enable, false to disable
		 * @since 2014.09
		 * @category Developer
		 */
		if( $config->get( 'controller/extjs/coupon/code/standard/enablecheck', true ) ) {
			$this->checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}

		$fileext = pathinfo( $fileinfo['name'], PATHINFO_EXTENSION );
		$dest = $dir . DIRECTORY_SEPARATOR . md5( $fileinfo['name'] . time() . getmypid() ) . '.' . $fileext;

		if( rename( $fileinfo['tmp_name'], $dest ) !== true )
		{
			$msg = sprintf( 'Uploaded file could not be moved to upload directory "%1$s"', $dir );
			throw new \Aimeos\Controller\ExtJS\Exception( $msg );
		}

		/** controller/extjs/coupon/code/standard/fileperms
		 * File permissions used when storing uploaded files
		 *
		 * The representation of the permissions is in octal notation (using 0-7)
		 * with a leading zero. The first number after the leading zero are the
		 * permissions for the web server creating the directory, the second is
		 * for the primary group of the web server and the last number represents
		 * the permissions for everyone else.
		 *
		 * You should use 0660 or 0600 for the permissions as the web server needs
		 * to manage the files. The group permissions are important if you plan
		 * to upload files directly via FTP or by other means because then the
		 * web server needs to be able to read and manage those files. In this
		 * case use 0660 as permissions, otherwise you can limit them to 0600.
		 *
		 * A more detailed description of the meaning of the Unix file permission
		 * bits can be found in the Wikipedia article about
		 * {@link https://en.wikipedia.org/wiki/File_system_permissions#Numeric_notation file system permissions}
		 *
		 * @param integer Octal Unix permission representation
		 * @since 2014.09
		 * @category Developer
		 */
		$perms = $config->get( 'controller/extjs/coupon/code/standard/fileperms', 0660 );
		if( chmod( $dest, $perms ) !== true )
		{
			$msg = sprintf( 'Could not set permissions "%1$s" for file "%2$s"', $perms, $dest );
			throw new \Aimeos\Controller\ExtJS\Exception( $msg );
		}

		$result = (object) array(
			'site' => $params->site,
			'items' => array(
				(object) array(
					'job.label' => 'Coupon code import: ' . $fileinfo['name'],
					'job.method' => 'Coupon_Code.importFile',
					'job.parameter' => array(
						'site' => $params->site,
						'parentid' => $params->parentid,
						'items' => $dest,
					),
					'job.status' => 1,
				),
			),
		);

		$jobController = \Aimeos\Controller\ExtJS\Admin\Job\Factory::createController( $this->getContext() );
		$jobController->saveItems( $result );

		return array(
			'items' => $dest,
			'success' => true,
		);
	}


	/**
	 * Imports a file with coupon codes and optional meta information.
	 *
	 * @param \stdClass $params Object containing the properties
	 */
	public function importFile( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'parentid', 'items' ) );
		$this->setLocale( $params->site );

		/** controller/extjs/coupon/code/standard/container/type
		 * Container file type storing all coupon code files to import
		 *
		 * All coupon code files or content objects must be put into one
		 * container file so editors don't have to upload one file for each
		 * coupon code file.
		 *
		 * The container file types that are supported by default are:
		 * * Zip
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Container file type
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/extjs/coupon/code/standard/container/format
		 */

		/** controller/extjs/coupon/code/standard/container/format
		 * Format of the coupon code files to import
		 *
		 * The coupon codes are stored in one or more files or content
		 * objects. The format of that file or content object can be configured
		 * with this option but most formats are bound to a specific container
		 * type.
		 *
		 * The formats that are supported by default are:
		 * * CSV (requires container type "Zip")
		 *
		 * Extensions implement other container types like spread sheets, XMLs or
		 * more advanced ways of handling the exported data.
		 *
		 * @param string Content file type
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/extjs/coupon/code/standard/container/type
		 * @see controller/extjs/coupon/code/standard/container/options
		 */

		/** controller/extjs/coupon/code/standard/container/options
		 * Options changing the expected format of the coupon codes to import
		 *
		 * Each content format may support some configuration options to change
		 * the output for that content type.
		 *
		 * The options for the CSV content format are:
		 * * csv-separator, default ','
		 * * csv-enclosure, default '"'
		 * * csv-escape, default '"'
		 * * csv-lineend, default '\n'
		 *
		 * For format options provided by other container types implemented by
		 * extensions, please have a look into the extension documentation.
		 *
		 * @param array Associative list of options with the name as key and its value
		 * @since 2014.09
		 * @category Developer
		 * @category User
		 * @see controller/extjs/coupon/code/standard/container/format
		 */

		$config = $this->getContext()->getConfig();

		$type = $config->get( 'controller/extjs/coupon/code/standard/container/type', 'Zip' );
		$format = $config->get( 'controller/extjs/coupon/code/standard/container/format', 'CSV' );
		$options = $config->get( 'controller/extjs/coupon/code/standard/container/options', [] );

		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $path )
		{
			$container = \Aimeos\MW\Container\Factory::getContainer( $path, $type, $format, $options );

			foreach( $container as $content ) {
				$this->importContent( $content, $params->parentid );
			}

			unlink( $path );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		$list = parent::getServiceDescription();

		$list['Coupon_Code.uploadFile'] = array(
			"parameters" => array(
				array( "type" => "string", "name" => "site", "optional" => false ),
				array( "type" => "string", "name" => "parentid", "optional" => false ),
			),
			"returns" => "array",
		);

		$list['Coupon_Code.importFile'] = array(
			"parameters" => array(
				array( "type" => "string", "name" => "site", "optional" => false ),
				array( "type" => "string", "name" => "parentid", "optional" => false ),
				array( "type" => "array", "name" => "items", "optional" => false ),
			),
			"returns" => "array",
		);

		return $list;
	}


	/**
	 * Returns the item populated by the data from the row.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Code\Iface $item Empty coupon item
	 * @param array $row List of coupon data (code, count, start and end)
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Populated coupon item
	 */
	protected function getItemBase( \Aimeos\MShop\Coupon\Item\Code\Iface $item, array $row )
	{
		foreach( $row as $idx => $value ) {
			$row[$idx] = trim( $value );
		}

		$count = ( isset( $row[1] ) && $row[1] != '' ? $row[1] : 1 );
		$start = ( isset( $row[2] ) && $row[2] != '' ? $row[2] : null );
		$end = ( isset( $row[3] ) && $row[3] != '' ? $row[3] : null );

		$item->setId( null );
		$item->setCode( $row[0] );
		$item->setCount( $count );
		$item->setDateStart( $start );
		$item->setDateEnd( $end );

		return $item;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'coupon/code' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'coupon.code';
	}


	/**
	 * Imports the coupon codes and meta data from the content object.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $content Content object with coupon codes and optional meta data
	 * @param string $couponId Unique ID of the coupon configuration for which the codes should be imported
	 * @throws \Exception If a code or its meta data can't be imported
	 */
	protected function importContent( \Aimeos\MW\Container\Content\Iface $content, $couponId )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'coupon/code' );

		$item = $manager->createItem();
		$item->setParentId( $couponId );

		$manager->begin();

		try
		{
			foreach( $content as $row )
			{
				if( trim( $row[0] ) == '' ) {
					continue;
				}

				$item = $this->getItemBase( $item, $row );
				$manager->saveItem( $item, false );
			}

			$manager->commit();
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			throw $e;
		}
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'coupon.code.datestart'} ) ) {
			$entry->{'coupon.code.datestart'} = str_replace( 'T', ' ', $entry->{'coupon.code.datestart'} );
		}

		if( isset( $entry->{'coupon.code.dateend'} ) ) {
			$entry->{'coupon.code.dateend'} = str_replace( 'T', ' ', $entry->{'coupon.code.dateend'} );
		}

		return $entry;
	}
}
