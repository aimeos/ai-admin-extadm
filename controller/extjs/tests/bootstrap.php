<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/*
 * Set error reporting to maximum
 */
error_reporting( -1 );
ini_set( 'display_errors', '1' );

date_default_timezone_set( 'UTC' );

/**
 * Set locale settings to reasonable defaults
 */
setlocale( LC_ALL, 'en_US.UTF-8' );
setlocale( LC_NUMERIC, 'POSIX' );
setlocale( LC_CTYPE, 'en_US.UTF-8' );
setlocale( LC_TIME, 'POSIX' );

/*
 * Set include path for tests
 */
define( 'PATH_TESTS', __DIR__ );
@mkdir( PATH_TESTS . DIRECTORY_SEPARATOR . 'tmp' );

require_once 'TestHelperExtjs.php';
\TestHelperExtjs::bootstrap();
