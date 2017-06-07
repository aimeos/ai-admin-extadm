<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage ExtJS
 */



namespace Aimeos\Controller\ExtJS\Common\Load\Text;


/**
 * ExtJS text export base class.
 *
 * @package Controller
 * @subpackage ExtJS
 */
abstract class Base
{
	private $context;
	private $textListTypes = [];
	private $textTypes = [];
	private $name;


	/**
	 * Initializes the controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param string $name Domain name of the export class
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, $name )
	{
		$this->context = $context;
		$this->name = $name;
	}


	/**
	 * Returns the schema of the item.
	 *
	 * @return array Associative list of "name" and "properties" list (including "description", "type" and "optional")
	 */
	public function getItemSchema()
	{
		return array(
			'name' => $this->name,
			'properties' => [],
		);
	}


	/**
	 * Returns the schema of the available search criteria and operators.
	 *
	 * @return array Associative list of "criteria" list (including "description", "type" and "optional") and "operators" list (including "compare", "combine" and "sort")
	 */
	public function getSearchSchema()
	{
		return array(
			'criteria' => [],
		);
	}


	/**
	 * Checks if the required parameter are available.
	 *
	 * @param \stdClass $params Item object containing the parameter
	 * @param string[] $names List of names of the required parameter
	 * @throws \Aimeos\Controller\ExtJS\Exception if a required parameter is missing
	 */
	protected function checkParams( \stdClass $params, array $names )
	{
		foreach( $names as $name )
		{
			if( !property_exists( $params, $name ) ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Missing parameter "%1$s"', $name ), -1 );
			}
		}
	}


	/**
	 * Returns the actual context item
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Associates the texts with the products.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object (attribute, product, etc.) for associating the list items
	 * @param array $itemTextMap Two dimensional associated list of codes and text IDs as key
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 */
	protected function importReferences( \Aimeos\MShop\Common\Manager\Iface $manager, array $itemTextMap, $domain )
	{
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', $domain . '.code', array_keys( $itemTextMap ) ) );
		$search->setSortations( array( $search->sort( '+', $domain . '.id' ) ) );

		$start = 0;
		$itemIdMap = $itemCodeMap = [];

		do
		{
			$result = $manager->searchItems( $search );

			foreach( $result as $item )
			{
				$itemIdMap[$item->getId()] = $item->getCode();
				$itemCodeMap[$item->getCode()] = $item->getId();
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );


		$listManager = $manager->getSubManager( 'lists' );

		$search = $listManager->createSearch();
		$expr = array(
				$search->compare( '==', $domain . '.lists.parentid', array_keys( $itemIdMap ) ),
				$search->compare( '==', $domain . '.lists.domain', 'text' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', $domain . '.lists.id' ) ) );

		$start = 0;

		do
		{
			$result = $listManager->searchItems( $search );

			foreach( $result as $item ) {
				unset( $itemTextMap[$itemIdMap[$item->getParentId()]][$item->getRefId()] );
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );


		$listTypes = $this->getTextListTypes( $manager, $domain );

		foreach( $itemTextMap as $itemCode => $textIds )
		{
			foreach( $textIds as $textId => $listType )
			{
				try
				{
					$iface = '\\Aimeos\\MShop\\Common\\Item\\Type\\Iface';
					if( !isset( $listTypes[$listType] ) || ( $listTypes[$listType] instanceof $iface ) === false ) {
						throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid list type "%1$s"', $listType ) );
					}

					$item = $listManager->createItem();
					$item->setParentId( $itemCodeMap[$itemCode] );
					$item->setTypeId( $listTypes[$listType]->getId() );
					$item->setDomain( 'text' );
					$item->setRefId( $textId );

					$listManager->saveItem( $item, false );
				}
				catch( \Exception $e ) {
					$this->getContext()->getLogger()->log( 'text reference: ' . $e->getMessage(), \Aimeos\MW\Logger\Base::ERR, 'import' );
				}
			}
		}
	}


	/**
	 * Returns a list of list type items.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object (attribute, product, etc.)
	 * @param string $domain Domain the list items must be associated to
	 * @return array Associative list of list type codes and items implementing \Aimeos\MShop\Common\Item\Type\Iface
	 */
	protected function getTextListTypes( \Aimeos\MShop\Common\Manager\Iface $manager, $domain )
	{
		if( isset( $this->textListTypes[$domain] ) ) {
			return $this->textListTypes[$domain];
		}

		$this->textListTypes[$domain] = [];

		$typeManager = $manager->getSubManager( 'lists' )->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', $domain . '.lists.type.domain', 'text' ) );
		$search->setSortations( array( $search->sort( '+', $domain . '.lists.type.code' ) ) );

		$start = 0;

		do
		{
			$result = $typeManager->searchItems( $search );

			foreach( $result as $typeItem ) {
				$this->textListTypes[$domain][$typeItem->getCode()] = $typeItem;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		return $this->textListTypes[$domain];
	}


	/**
	 * Returns a list of text type items.
	 *
	 * @param string $domain Domain the text items must be associated to
	 * @return array List of text type items implementing \Aimeos\MShop\Common\Item\Type\Iface
	 */
	protected function getTextTypes( $domain )
	{
		if( isset( $this->textTypes[$domain] ) ) {
			return $this->textTypes[$domain];
		}

		$this->textTypes[$domain] = [];

		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->getContext() );
		$manager = $textManager->getSubManager( 'type' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.type.domain', $domain ) );
		$search->setSortations( array( $search->sort( '+', 'text.type.code' ) ) );

		$start = 0;

		do
		{
			$result = $manager->searchItems( $search );

			$this->textTypes[$domain] = array_merge( $this->textTypes[$domain], $result );

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count > 0 );

		return $this->textTypes[$domain];
	}


	/**
	 * Creates a new locale object and adds this object to the context.
	 *
	 * @param string $site Site code
	 */
	protected function setLocale( $site )
	{
		$context = $this->getContext();
		$locale = $context->getLocale();

		$siteItem = null;
		$siteManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context )->getSubManager( 'site' );

		if( $site != '' )
		{
			$search = $siteManager->createSearch();
			$search->setConditions( $search->compare( '==', 'locale.site.code', $site ) );
			$sites = $siteManager->searchItems( $search );

			if( ( $siteItem = reset( $sites ) ) === false ) {
				throw new \Aimeos\Controller\ExtJS\Exception( 'Site item not found.' );
			}
		}

		$localeItem = new \Aimeos\MShop\Locale\Item\Standard( [], $siteItem );
		$localeItem->setLanguageId( $locale->getLanguageId() );
		$localeItem->setCurrencyId( $locale->getCurrencyId() );

		if( $siteItem !== null ) {
			$localeItem->setSiteId( $siteItem->getId() );
		}

		$context->setLocale( $localeItem );
	}


	/**
	 * Stores the uploaded file and returns the path to this file
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request PSR-7 request object
	 * @param string $clientFilename Result parameter for the file name sent by the client
	 * @throws \Aimeos\Controller\ExtJS\Exception If no file was uploaded or an error occured
	 * @return string Path to the stored file
	 */
	protected function storeFile( \Psr\Http\Message\ServerRequestInterface $request, &$clientFilename = '' )
	{
		$context = $this->getContext();
		$files = (array) $request->getUploadedFiles();

		if( ( $file = reset( $files ) ) === false || $file->getError() !== UPLOAD_ERR_OK ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded or an error occured' );
		}

		$clientFilename = $file->getClientFilename();
		$fileext = pathinfo( $clientFilename, PATHINFO_EXTENSION );
		$dest = md5( $clientFilename . time() . getmypid() ) . '.' . $fileext;

		$fs = $context->getFilesystemManager()->get( 'fs-admin' );
		$fs->writes( $dest, $file->getStream()->detach() );

		return $dest;
	}


	/**
	 * Imports the text content using the given text types.
	 *
	 * @param \Aimeos\MW\Container\Content\Iface $contentItem Content item containing texts and associated data
	 * @param array $textTypeMap Associative list of text type IDs as keys and text type codes as values
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 * @return void
	 */
	protected function importTextsFromContent( \Aimeos\MW\Container\Content\Iface $contentItem, array $textTypeMap, $domain )
	{
		$count = 0;
		$codeIdMap = [];
		$context = $this->getContext();
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $context );
		$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );

		$contentItem->next(); // skip description row

		while( ( $row = $contentItem->current() ) !== null )
		{
			$codeIdMap = $this->importTextRow( $textManager, $row, $textTypeMap, $codeIdMap, $domain );

			if( ++$count == 1000 )
			{
				$this->importReferences( $manager, $codeIdMap, $domain );
				$codeIdMap = [];
				$count = 0;
			}

			$contentItem->next();
		}

		if( !empty( $codeIdMap ) ) {
			$this->importReferences( $manager, $codeIdMap, $domain );
		}
	}


	/**
	 * Inserts a single text item from the given import row.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $textManager Text manager object
	 * @param array $row Row from import file
	 * @param array $codeIdMap Two dimensional associated list of codes and text IDs as key
	 * @param array $textTypeMap Associative list of text type IDs as keys and text type codes as values
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 * @throws \Aimeos\Controller\ExtJS\Exception If text type is invalid
	 */
	private function importTextRow( \Aimeos\MShop\Common\Manager\Iface $textManager, array $row, array $textTypeMap,
		array $codeIdMap, $domain )
	{
		if( count( $row ) !== 7 )
		{
			$msg = sprintf( 'Invalid row from %1$s text import: %2$s', $domain, print_r( $row, true ) );
			$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'import' );
		}

		try
		{
			$textType = isset( $row[4] ) ? $row[4] : null;

			if( !isset( $textTypeMap[$textType] ) ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid text type "%1$s"', $textType ) );
			}

			$codeIdMap = $this->saveTextItem( $textManager, $row, $textTypeMap, $codeIdMap, $domain );
		}
		catch( \Exception $e )
		{
			$msg = sprintf( 'Error in %1$s text import: %2$s', $domain, $e->getMessage() );
			$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::ERR, 'import' );
		}

		return $codeIdMap;
	}


	/**
	 * Saves a text item from the given data.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $textManager Text manager object
	 * @param array $row Row from import file
	 * @param array $textTypeMap Associative list of text type IDs as keys and text type codes as values
	 * @param array $codeIdMap Two dimensional associated list of codes and text IDs as key
	 * @param string $domain Name of the domain this text belongs to, e.g. product, catalog, attribute
	 * @return array Updated two dimensional associated list of codes and text IDs as key
	 */
	private function saveTextItem( \Aimeos\MShop\Common\Manager\Iface $textManager, array $row,
		array $textTypeMap, array $codeIdMap, $domain )
	{
		$value = isset( $row[6] ) ? $row[6] : '';
		$textId = isset( $row[5] ) ? $row[5] : '';

		if( $textId != '' || $value != '' )
		{
			$item = $textManager->createItem();

			if( $textId != '' ) {
				$item->setId( $textId );
			}

			$item->setLanguageId( ( $row[0] != '' ? $row[0] : null ) );
			$item->setTypeId( $textTypeMap[$row[4]] );
			$item->setDomain( $domain );
			$item->setLabel( mb_strcut( $value, 0, 255 ) );
			$item->setContent( $value );
			$item->setStatus( 1 );

			$item = $textManager->saveItem( $item );

			$codeIdMap[$row[2]][$item->getId()] = $row[3];
		}

		return $codeIdMap;
	}


	/**
	 * Creates container for storing export files.
	 *
	 * @param string $resource Path to the file
	 * @param string $key Configuration key prefix for the container type/format/options keys
	 * @return \Aimeos\MW\Container\Iface Container item
	 */
	protected function createContainer( $resource, $key )
	{
		$config = $this->getContext()->getConfig();

		$type = $config->get( $key . '/type', 'Zip' );
		$format = $config->get( $key . '/format', 'CSV' );
		$options = $config->get( $key . '/options', [] );

		return \Aimeos\MW\Container\Factory::getContainer( $resource, $type, $format, $options );
	}
}