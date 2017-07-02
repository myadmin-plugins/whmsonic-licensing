<?php

namespace Detain\MyAdminWhmsonic;

use Symfony\Component\EventDispatcher\GenericEvent;

class Plugin {

	public static $name = 'Whmsonic Licensing';
	public static $description = 'Allows selling of Whmsonic Server and VPS License Types.  More info at https://www.netenberg.com/whmsonic.php';
	public static $help = 'It provides more than one million end users the ability to quickly install dozens of the leading open source content management systems into their web space.  	Must have a pre-existing cPanel license with cPanelDirect to purchase a whmsonic license. Allow 10 minutes for activation.';
	public static $module = 'licenses';
	public static $type = 'service';


	public function __construct() {
	}

	public static function getHooks() {
		return [
			'function.requirements' => [__CLASS__, 'getRequirements'],
			self::$module.'.settings' => [__CLASS__, 'getSettings'],
			self::$module.'.activate' => [__CLASS__, 'getActivate'],
			self::$module.'.reactivate' => [__CLASS__, 'getActivate'],
		];
	}

	public static function getActivate(GenericEvent $event) {
		$serviceClass = $event->getSubject();
		if ($event['category'] == SERVICE_TYPES_WHMSONIC) {
			myadmin_log(self::$module, 'info', 'Whmsonic Activation', __LINE__, __FILE__);
			function_requirements('activate_whmsonic');
			activate_whmsonic($serviceClass->getIp(), $event['field1'], $serviceClass->getId(), $event['email'], $event['email']);
			$event->stopPropagation();
		}
	}

	public static function getChangeIp(GenericEvent $event) {
		if ($event['category'] == SERVICE_TYPES_WHMSONIC) {
			$serviceClass = $event->getSubject();
			$settings = get_module_settings(self::$module);
			$whmsonic = new Whmsonic(WHMSONIC_USERNAME, WHMSONIC_PASSWORD);
			myadmin_log(self::$module, 'info', "IP Change - (OLD:".$serviceClass->getIp().") (NEW:{$event['newip']})", __LINE__, __FILE__);
			$result = $whmsonic->editIp($serviceClass->getIp(), $event['newip']);
			if (isset($result['faultcode'])) {
				myadmin_log(self::$module, 'error', 'Whmsonic editIp('.$serviceClass->getIp().', '.$event['newip'].') returned Fault '.$result['faultcode'].': '.$result['fault'], __LINE__, __FILE__);
				$event['status'] = 'error';
				$event['status_text'] = 'Error Code '.$result['faultcode'].': '.$result['fault'];
			} else {
				$GLOBALS['tf']->history->add($settings['TABLE'], 'change_ip', $event['newip'], $serviceClass->getIp());
				$serviceClass->set_ip($event['newip'])->save();
				$event['status'] = 'ok';
				$event['status_text'] = 'The IP Address has been changed.';
			}
			$event->stopPropagation();
		}
	}

	public static function getMenu(GenericEvent $event) {
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			$menu->add_link(self::$module, 'choice=none.reusable_whmsonic', 'icons/database_warning_48.png', 'ReUsable Whmsonic Licenses');
			$menu->add_link(self::$module, 'choice=none.whmsonic_list', 'icons/database_warning_48.png', 'Whmsonic Licenses Breakdown');
			$menu->add_link(self::$module.'api', 'choice=none.whmsonic_licenses_list', 'whm/createacct.gif', 'List all Whmsonic Licenses');
		}
	}

	public static function getRequirements(GenericEvent $event) {
		$loader = $event->getSubject();
		$loader->add_requirement('activate_whmsonic', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_terminate', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_suspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_unsuspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_list', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_verify', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
	}

	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_text_setting(self::$module, 'WHMSonic', 'whmsonic_username', 'Whmsonic Username:', 'Whmsonic Username', $settings->get_setting('WHMSONIC_USERNAME'));
		$settings->add_text_setting(self::$module, 'WHMSonic', 'whmsonic_password', 'Whmsonic Password:', 'Whmsonic Password', $settings->get_setting('WHMSONIC_PASSWORD'));
		$settings->add_dropdown_setting(self::$module, 'WHMSonic', 'outofstock_licenses_whmsonic', 'Out Of Stock Whmsonic Licenses', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_LICENSES_WHMSONIC'), array('0', '1'), array('No', 'Yes',));
	}

}
