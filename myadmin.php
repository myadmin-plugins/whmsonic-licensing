<?php
/* TODO:
 - service type, category, and services  adding
 - dealing with the SERVICE_TYPES_whmsonic define
 - add way to call/hook into install/uninstall
*/
return [
	'name' => 'Whmsonic Licensing',
	'description' => 'Allows selling of Whmsonic Server and VPS License Types.  More info at https://www.netenberg.com/whmsonic.php',
	'help' => 'It provides more than one million end users the ability to quickly install dozens of the leading open source content management systems into their web space.  	Must have a pre-existing cPanel license with cPanelDirect to purchase a whmsonic license. Allow 10 minutes for activation.',
	'module' => 'licenses',
	'author' => 'detain@interserver.net',
	'home' => 'https://github.com/detain/myadmin-whmsonic-licensing',
	'repo' => 'https://github.com/detain/myadmin-whmsonic-licensing',
	'version' => '1.0.0',
	'type' => 'licenses',
	'hooks' => [
		'function.requirements' => ['Detain\MyAdminWhmsonic\Plugin', 'Requirements'],
		'licenses.settings' => ['Detain\MyAdminWhmsonic\Plugin', 'Settings'],
		/* 'licenses.activate' => ['Detain\MyAdminWhmsonic\Plugin', 'Activate'],
		'licenses.change_ip' => ['Detain\MyAdminWhmsonic\Plugin', 'ChangeIp'],
		'ui.menu' => ['Detain\MyAdminWhmsonic\Plugin', 'Menu'] */
	],
];
