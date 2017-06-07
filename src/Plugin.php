<?php

namespace Detain\MyAdminWhmsonic;

use Symfony\Component\EventDispatcher\GenericEvent;

class Plugin {

	public function __construct() {
	}

	public static function Activate(GenericEvent $event) {
		// will be executed when the licenses.license event is dispatched
		$license = $event->getSubject();
		if ($event['category'] == SERVICE_TYPES_WHMSONIC) {
			myadmin_log('licenses', 'info', 'Whmsonic Activation', __LINE__, __FILE__);
			$data = $GLOBALS['tf']->accounts->read($custid);
			function_requirements('activate_whmsonic');
			activate_whmsonic($license->get_ip(), $event['field1'], $license->get_id(), $event['email'], $event['email']);
			$event->stopPropagation();
		}
	}

	public static function ChangeIp(GenericEvent $event) {
		if ($event['category'] == SERVICE_TYPES_WHMSONIC) {
			$license = $event->getSubject();
			$settings = get_module_settings('licenses');
			$whmsonic = new Whmsonic(WHMSONIC_USERNAME, WHMSONIC_PASSWORD);
			myadmin_log('licenses', 'info', "IP Change - (OLD:".$license->get_ip().") (NEW:{$event['newip']})", __LINE__, __FILE__);
			$result = $whmsonic->editIp($license->get_ip(), $event['newip']);
			if (isset($result['faultcode'])) {
				myadmin_log('licenses', 'error', 'Whmsonic editIp('.$license->get_ip().', '.$event['newip'].') returned Fault '.$result['faultcode'].': '.$result['fault'], __LINE__, __FILE__);
				$event['status'] = 'error';
				$event['status_text'] = 'Error Code '.$result['faultcode'].': '.$result['fault'];
			} else {
				$GLOBALS['tf']->history->add($settings['TABLE'], 'change_ip', $event['newip'], $license->get_ip());
				$license->set_ip($event['newip'])->save();
				$event['status'] = 'ok';
				$event['status_text'] = 'The IP Address has been changed.';
			}
			$event->stopPropagation();
		}
	}

	public static function Menu(GenericEvent $event) {
		// will be executed when the licenses.settings event is dispatched
		$menu = $event->getSubject();
		$module = 'licenses';
		if ($GLOBALS['tf']->ima == 'admin') {
			$menu->add_link($module, 'choice=none.reusable_whmsonic', 'icons/database_warning_48.png', 'ReUsable Whmsonic Licenses');
			$menu->add_link($module, 'choice=none.whmsonic_list', 'icons/database_warning_48.png', 'Whmsonic Licenses Breakdown');
			$menu->add_link($module.'api', 'choice=none.whmsonic_licenses_list', 'whm/createacct.gif', 'List all Whmsonic Licenses');
		}
	}

	public static function Requirements(GenericEvent $event) {
		// will be executed when the licenses.loader event is dispatched
		$loader = $event->getSubject();
		$loader->add_requirement('activate_whmsonic', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_terminate', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_suspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_unsuspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_list', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
		$loader->add_requirement('whmsonic_verify', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
	}

	public static function Settings(GenericEvent $event) {
		// will be executed when the licenses.settings event is dispatched
		$settings = $event->getSubject();
		$settings->add_text_setting('licenses', 'apisettings', 'whmsonic_username', 'Whmsonic Username:', 'Whmsonic Username', $settings->get_setting('WHMSONIC_USERNAME'));
		$settings->add_text_setting('licenses', 'apisettings', 'whmsonic_password', 'Whmsonic Password:', 'Whmsonic Password', $settings->get_setting('WHMSONIC_PASSWORD'));
		$settings->add_dropdown_setting('stock', 'outofstock_licenses_whmsonic', 'Out Of Stock Whmsonic Licenses', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_LICENSES_WHMSONIC'), array('0', '1'), array('No', 'Yes', ));
	}

}
