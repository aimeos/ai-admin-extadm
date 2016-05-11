<?php

return array(
	'name' => 'ai-admin-extadm',
	'depends' => array(
		'aimeos-core',
	),
	'include' => array(
		'controller/extjs/src',
		'controller/jobs/src',
	),
	'i18n' => array(
		'admin' => 'admin/i18n',
		'admin/ext' => 'admin/i18n/ext',
		'controller/extjs' => 'controller/extjs/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
	),
	'custom' => array(
		'admin/extjs' => array(
			'admin/extjs/manifest.jsb2',
		),
		'controller/extjs' => array(
			'controller/extjs/src',
		),
		'controller/jobs' => array(
			'controller/jobs/src',
		),
	),
);
