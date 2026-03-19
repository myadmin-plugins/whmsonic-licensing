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
         * @param mixed ...$args
         * @return void
         */
        function myadmin_log(...$args): void
        {
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
         * @param mixed ...$args
         * @return string
         */
        function activate_whmsonic(...$args): string
        {
            return 'success';
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
