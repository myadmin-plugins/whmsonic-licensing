<?php

namespace Detain\MyAdminWhmsonic;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminWhmsonic
 */
class Plugin
{
    public static $name = 'WHMSonic Licensing';
    public static $description = 'Allows selling of WHMSonic License Types. WHMSonic is the best WHM/cPanel plugin(shoutcast control panel), intended on making your life easier. You can install it with a single SSH command in seconds and it allows you to offer shoutcast, icecast, streaming media hosting, AutoDJ, radio reseller from your Dedicated or VPS server. It has powerful control panel for your cpanel clients and resellers. Clients can fully control their radios easily. It has everything you need to start and manage your shoutcast radio service hosting.  More info at https://www.whmsonic.com/';
    public static $help = '';
    public static $module = 'licenses';
    public static $type = 'service';

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function getHooks()
    {
        return [
            'function.requirements' => [__CLASS__, 'getRequirements'],
            self::$module.'.settings' => [__CLASS__, 'getSettings'],
            self::$module.'.activate' => [__CLASS__, 'getActivate'],
            self::$module.'.reactivate' => [__CLASS__, 'getActivate']
        ];
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getActivate(GenericEvent $event)
    {
        $serviceClass = $event->getSubject();
        if ($event['category'] == get_service_define('WHMSONIC')) {
            myadmin_log(self::$module, 'info', 'WHMSonic Activation', __LINE__, __FILE__, self::$module, $serviceClass->getId());
            function_requirements('activate_whmsonic');
            activate_whmsonic($serviceClass->getIp(), $event['field1'], $serviceClass->getId(), $event['email'], $event['email']);
            $event->stopPropagation();
        }
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getChangeIp(GenericEvent $event)
    {
        if ($event['category'] == get_service_define('WHMSONIC')) {
            $serviceClass = $event->getSubject();
            $settings = get_module_settings(self::$module);
            $whmsonic = new Whmsonic(WHMSONIC_USERNAME, WHMSONIC_PASSWORD);
            myadmin_log(self::$module, 'info', 'IP Change - (OLD:' .$serviceClass->getIp().") (NEW:{$event['newip']})", __LINE__, __FILE__, self::$module, $serviceClass->getId());
            $result = $whmsonic->editIp($serviceClass->getIp(), $event['newip']);
            if (isset($result['faultcode'])) {
                myadmin_log(self::$module, 'error', 'WHMSonic editIp('.$serviceClass->getIp().', '.$event['newip'].') returned Fault '.$result['faultcode'].': '.$result['fault'], __LINE__, __FILE__, self::$module, $serviceClass->getId());
                $event['status'] = 'error';
                $event['status_text'] = 'Error Code '.$result['faultcode'].': '.$result['fault'];
            } else {
                $GLOBALS['tf']->history->add($settings['TABLE'], 'change_ip', $event['newip'], $serviceClass->getId(), $serviceClass->getCustid());
                $serviceClass->set_ip($event['newip'])->save();
                $event['status'] = 'ok';
                $event['status_text'] = 'The IP Address has been changed.';
            }
            $event->stopPropagation();
        }
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getMenu(GenericEvent $event)
    {
        $menu = $event->getSubject();
        if ($GLOBALS['tf']->ima == 'admin') {
            $menu->add_link(self::$module, 'choice=none.reusable_whmsonic', '/images/myadmin/to-do.png', _('ReUsable WHMSonic Licenses'));
            $menu->add_link(self::$module, 'choice=none.whmsonic_list', '/images/myadmin/to-do.png', _('WHMSonic Licenses Breakdown'));
            $menu->add_link(self::$module.'api', 'choice=none.whmsonic_licenses_list', '/images/whm/createacct.gif', _('List all WHMSonic Licenses'));
        }
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getRequirements(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Plugins\Loader $this->loader
         */
        $loader = $event->getSubject();
        $loader->add_requirement('activate_whmsonic', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
        $loader->add_page_requirement('whmsonic_terminate', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
        $loader->add_page_requirement('whmsonic_suspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
        $loader->add_page_requirement('whmsonic_unsuspend', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
        $loader->add_page_requirement('whmsonic_list', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
        $loader->add_page_requirement('whmsonic_verify', '/../vendor/detain/myadmin-whmsonic-licensing/src/whmsonic.inc.php');
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getSettings(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Settings $settings
         **/
        $settings = $event->getSubject();
        $settings->add_text_setting(self::$module, _('WHMSonic'), 'whmsonic_username', _('WHMSonic Username'), _('WHMSonic Username'), $settings->get_setting('WHMSONIC_USERNAME'));
        $settings->add_password_setting(self::$module, _('WHMSonic'), 'whmsonic_password', _('WHMSonic Password'), _('WHMSonic Password'), $settings->get_setting('WHMSONIC_PASSWORD'));
        $settings->add_dropdown_setting(self::$module, _('WHMSonic'), 'outofstock_licenses_whmsonic', _('Out Of Stock WHMSonic Licenses'), _('Enable/Disable Sales Of This Type'), $settings->get_setting('OUTOFSTOCK_LICENSES_WHMSONIC'), ['0', '1'], ['No', 'Yes']);
    }
}
