<?php

/**
 * Namespace-level polyfill stubs for functions called by Plugin.php.
 *
 * Plugin.php calls these functions without a leading backslash, so PHP
 * resolves them in the Detain\MyAdminWhmsonic namespace first. These
 * stubs provide safe no-op implementations for isolated unit testing.
 */

namespace Detain\MyAdminWhmsonic {

    if (!function_exists('Detain\\MyAdminWhmsonic\\get_service_define')) {
        /**
         * @param string $name
         * @return string
         */
        function get_service_define(string $name): string
        {
            return 'WHMSONIC_TYPE';
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\myadmin_log')) {
        /**
         * Forwards to the global myadmin_log() when one exists.
         *
         * IT MUST NOT SWALLOW. Plugin.php calls myadmin_log() unqualified, and PHP binds that
         * to this namespaced declaration before the global one -- so a plain no-op here takes
         * the call away from whoever installed the global. Under the contract harness that
         * global IS the observer: it is how assertions S-1 and S-2 see whether a lifecycle
         * handler acted.
         *
         * Swallowing it broke both assertions, in opposite and equally wrong ways. S-1 read the
         * empty recorder as proof the handler was dead code and reported this plugin's service
         * as one that "silently never gets provisioned" -- false (fixed harness-side in v2.1.1,
         * which now skips instead). S-2 read the same empty recorder as proof the handler was
         * correctly inert for foreign service types -- a silent pass that would have held even
         * if the handler acted on every service in the fleet.
         *
         * Forwarding satisfies both readers: the no-op behaviour is preserved when nothing else
         * defines the global, and the harness sees every call when it does.
         *
         * @param mixed ...$args
         * @return void
         */
        function myadmin_log(...$args): void
        {
            if (\function_exists('\myadmin_log')) {
                \myadmin_log(...$args);
            }
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\function_requirements')) {
        /**
         * @param mixed ...$args
         * @return void
         */
        function function_requirements(...$args): void
        {
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\activate_whmsonic')) {
        /**
         * Records its arguments and returns whatever the test asked for.
         *
         * The real activate_whmsonic() in src/whmsonic.inc.php returns the string
         * 'success' when the WHMSonic API replies 'Complete', and the error text
         * otherwise -- so 'success' is the correct default here. Tests drive the
         * error path by setting $GLOBALS['whmsonic_test_activate_response'].
         *
         * @param mixed ...$args
         * @return string
         */
        function activate_whmsonic(...$args): string
        {
            $GLOBALS['whmsonic_test_activate_calls'][] = $args;

            return $GLOBALS['whmsonic_test_activate_response'] ?? 'success';
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\chatNotify')) {
        /**
         * Records chat notifications so tests can assert on them.
         *
         * @param string               $msg
         * @param string               $where
         * @param array<string, mixed> $extra
         * @return void
         */
        function chatNotify(string $msg, string $where = 'notifications', array $extra = []): void
        {
            $GLOBALS['whmsonic_test_chat_notifications'][] = ['msg' => $msg, 'where' => $where];
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\get_module_settings')) {
        /**
         * @param string $module
         * @return array<string, mixed>
         */
        function get_module_settings(string $module): array
        {
            return ['TABLE' => 'test_table'];
        }
    }

    if (!function_exists('Detain\\MyAdminWhmsonic\\_')) {
        /**
         * @param string $message
         * @return string
         */
        function _(string $message): string
        {
            return $message;
        }
    }
}
