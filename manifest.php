<?php

return array(
	'name' => 'ai-admin-extadm',
	'depends' => array(
		'aimeos-core',
	),
	'include' => array(
		'controller/extjs/src',
	),
	'i18n' => array(
		'controller/extjs' => 'controller/extjs/i18n',
	),
	'custom' => array(
		'admin/extjs' => array(
			'admin/extjs/manifest.jsb2',
		),
		'controller/extjs' => array(
			'controller/extjs/src',
		),
	),
);
