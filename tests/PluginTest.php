<?php

declare(strict_types=1);

namespace Detain\MyAdminWhmsonic\Tests;

use Detain\MyAdminWhmsonic\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Test suite for the Plugin class.
 *
 * Validates class structure, static properties, hook registration,
 * and event handler signatures for the WHMSonic licensing plugin.
 *
 * @covers \Detain\MyAdminWhmsonic\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass<Plugin>
     */
    private ReflectionClass $reflection;

    /**
     * Set up reflection instance before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Plugin::class);
    }

    /**
     * Test that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testPluginCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Test that the Plugin class resides in the correct namespace.
     *
     * @return void
     */
    public function testPluginClassNamespace(): void
    {
        $this->assertSame('Detain\\MyAdminWhmsonic', $this->reflection->getNamespaceName());
    }

    /**
     * Test that the $name static property is set correctly.
     *
     * @return void
     */
    public function testNamePropertyValue(): void
    {
        $this->assertSame('WHMSonic Licensing', Plugin::$name);
    }

    /**
     * Test that the $description static property is a non-empty string.
     *
     * @return void
     */
    public function testDescriptionPropertyIsNonEmpty(): void
    {
        $this->assertIsString(Plugin::$description);
        $this->assertNotEmpty(Plugin::$description);
    }

    /**
     * Test that the $description static property contains expected keywords.
     *
     * @return void
     */
    public function testDescriptionContainsExpectedKeywords(): void
    {
        $this->assertStringContainsString('WHMSonic', Plugin::$description);
        $this->assertStringContainsString('shoutcast', Plugin::$description);
    }

    /**
     * Test that the $help static property exists and is a string.
     *
     * @return void
     */
    public function testHelpPropertyIsString(): void
    {
        $this->assertIsString(Plugin::$help);
    }

    /**
     * Test that the $module static property is set to 'licenses'.
     *
     * @return void
     */
    public function testModulePropertyValue(): void
    {
        $this->assertSame('licenses', Plugin::$module);
    }

    /**
     * Test that the $type static property is set to 'service'.
     *
     * @return void
     */
    public function testTypePropertyValue(): void
    {
        $this->assertSame('service', Plugin::$type);
    }

    /**
     * Test that all expected static properties exist on the class.
     *
     * @return void
     */
    public function testAllStaticPropertiesExist(): void
    {
        $expectedProperties = ['name', 'description', 'help', 'module', 'type'];
        foreach ($expectedProperties as $property) {
            $this->assertTrue(
                $this->reflection->hasProperty($property),
                "Expected static property \${$property} to exist on Plugin class"
            );
            $this->assertTrue(
                $this->reflection->getProperty($property)->isStatic(),
                "Expected \${$property} to be static"
            );
            $this->assertTrue(
                $this->reflection->getProperty($property)->isPublic(),
                "Expected \${$property} to be public"
            );
        }
    }

    /**
     * Test that getHooks returns an array.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
    }

    /**
     * Test that getHooks contains all required event keys.
     *
     * @return void
     */
    public function testGetHooksContainsRequiredKeys(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertArrayHasKey('function.requirements', $hooks);
        $this->assertArrayHasKey('licenses.settings', $hooks);
        $this->assertArrayHasKey('licenses.activate', $hooks);
        $this->assertArrayHasKey('licenses.reactivate', $hooks);
    }

    /**
     * Test that getHooks does not return an empty array.
     *
     * @return void
     */
    public function testGetHooksIsNotEmpty(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertNotEmpty($hooks);
    }

    /**
     * Test that each hook value is a valid callable-style array.
     *
     * @return void
     */
    public function testGetHooksValuesAreCallableArrays(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $eventName => $callback) {
            $this->assertIsArray($callback, "Hook for '{$eventName}' should be an array");
            $this->assertCount(2, $callback, "Hook for '{$eventName}' should have exactly 2 elements");
            $this->assertSame(
                Plugin::class,
                $callback[0],
                "Hook for '{$eventName}' should reference Plugin class"
            );
            $this->assertIsString($callback[1], "Hook method name for '{$eventName}' should be a string");
        }
    }

    /**
     * Test that all hook callback methods exist on the Plugin class.
     *
     * @return void
     */
    public function testGetHooksMethodsExist(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $eventName => $callback) {
            $this->assertTrue(
                $this->reflection->hasMethod($callback[1]),
                "Method {$callback[1]} referenced in hook '{$eventName}' does not exist on Plugin class"
            );
        }
    }

    /**
     * Test that all hook callback methods are public and static.
     *
     * @return void
     */
    public function testGetHooksMethodsArePublicStatic(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $eventName => $callback) {
            $method = $this->reflection->getMethod($callback[1]);
            $this->assertTrue(
                $method->isPublic(),
                "Method {$callback[1]} should be public"
            );
            $this->assertTrue(
                $method->isStatic(),
                "Method {$callback[1]} should be static"
            );
        }
    }

    /**
     * Test that the activate and reactivate hooks point to the same method.
     *
     * @return void
     */
    public function testActivateAndReactivateShareSameHandler(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame($hooks['licenses.activate'], $hooks['licenses.reactivate']);
    }

    /**
     * Test that hook keys use the module property value as prefix where applicable.
     *
     * @return void
     */
    public function testHookKeysUseModulePrefix(): void
    {
        $hooks = Plugin::getHooks();
        $module = Plugin::$module;
        $moduleHooks = [
            "{$module}.settings",
            "{$module}.activate",
            "{$module}.reactivate",
        ];
        foreach ($moduleHooks as $hookKey) {
            $this->assertArrayHasKey($hookKey, $hooks, "Expected hook key '{$hookKey}' to exist");
        }
    }

    /**
     * Test that the getActivate method accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetActivateMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getActivate');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that the getChangeIp method accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetChangeIpMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getChangeIp');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that the getMenu method accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetMenuMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that the getRequirements method accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetRequirementsMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that the getSettings method accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetSettingsMethodSignature(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());
        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Test that all event handler methods exist on the Plugin class.
     *
     * @return void
     */
    public function testAllEventHandlerMethodsExist(): void
    {
        $expectedMethods = [
            'getActivate',
            'getChangeIp',
            'getMenu',
            'getRequirements',
            'getSettings',
        ];
        foreach ($expectedMethods as $methodName) {
            $this->assertTrue(
                $this->reflection->hasMethod($methodName),
                "Expected method {$methodName} to exist on Plugin class"
            );
        }
    }

    /**
     * Test that all event handler methods are static.
     *
     * @return void
     */
    public function testAllEventHandlerMethodsAreStatic(): void
    {
        $expectedMethods = [
            'getActivate',
            'getChangeIp',
            'getMenu',
            'getRequirements',
            'getSettings',
        ];
        foreach ($expectedMethods as $methodName) {
            $method = $this->reflection->getMethod($methodName);
            $this->assertTrue(
                $method->isStatic(),
                "Method {$methodName} should be static"
            );
        }
    }

    /**
     * Test that the constructor has no required parameters.
     *
     * @return void
     */
    public function testConstructorHasNoRequiredParameters(): void
    {
        $constructor = $this->reflection->getConstructor();
        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $requiredParams = array_filter($params, function ($param) {
            return !$param->isOptional();
        });
        $this->assertCount(0, $requiredParams);
    }

    /**
     * Test that the getHooks method is static.
     *
     * @return void
     */
    public function testGetHooksIsStatic(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertTrue($method->isStatic());
    }

    /**
     * Test that the getHooks method has no parameters.
     *
     * @return void
     */
    public function testGetHooksHasNoParameters(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertCount(0, $method->getParameters());
    }

    /**
     * Test that getRequirements registers the expected file paths via the loader subject.
     *
     * @return void
     */
    public function testGetRequirementsRegistersExpectedPaths(): void
    {
        $registeredRequirements = [];
        $registeredPageRequirements = [];

        $loader = new class($registeredRequirements, $registeredPageRequirements) {
            /** @var array<int, array{string, string}> */
            private array $reqs;
            /** @var array<int, array{string, string}> */
            private array $pageReqs;

            /**
             * @param array<int, array{string, string}> $reqs
             * @param array<int, array{string, string}> $pageReqs
             */
            public function __construct(array &$reqs, array &$pageReqs)
            {
                $this->reqs = &$reqs;
                $this->pageReqs = &$pageReqs;
            }

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_requirement(string $name, string $path): void
            {
                $this->reqs[] = [$name, $path];
            }

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_page_requirement(string $name, string $path): void
            {
                $this->pageReqs[] = [$name, $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $this->assertCount(1, $registeredRequirements);
        $this->assertSame('activate_whmsonic', $registeredRequirements[0][0]);
        $this->assertStringContainsString('whmsonic.inc.php', $registeredRequirements[0][1]);

        $this->assertCount(5, $registeredPageRequirements);

        $expectedPageFunctions = [
            'whmsonic_terminate',
            'whmsonic_suspend',
            'whmsonic_unsuspend',
            'whmsonic_list',
            'whmsonic_verify',
        ];
        foreach ($registeredPageRequirements as $index => $entry) {
            $this->assertSame($expectedPageFunctions[$index], $entry[0]);
            $this->assertStringContainsString('whmsonic.inc.php', $entry[1]);
        }
    }

    /**
     * Test that getSettings calls expected setting methods on the settings subject.
     *
     * @return void
     */
    public function testGetSettingsRegistersExpectedSettings(): void
    {
        $textSettings = [];
        $passwordSettings = [];
        $dropdownSettings = [];

        $settings = new class($textSettings, $passwordSettings, $dropdownSettings) {
            /** @var array<int, array<int, mixed>> */
            private array $text;
            /** @var array<int, array<int, mixed>> */
            private array $password;
            /** @var array<int, array<int, mixed>> */
            private array $dropdown;

            /**
             * @param array<int, array<int, mixed>> $text
             * @param array<int, array<int, mixed>> $password
             * @param array<int, array<int, mixed>> $dropdown
             */
            public function __construct(array &$text, array &$password, array &$dropdown)
            {
                $this->text = &$text;
                $this->password = &$password;
                $this->dropdown = &$dropdown;
            }

            /**
             * @param string $setting
             * @return string
             */
            public function get_setting(string $setting): string
            {
                return 'mock_value';
            }

            /**
             * @param mixed ...$args
             * @return void
             */
            public function add_text_setting(...$args): void
            {
                $this->text[] = $args;
            }

            /**
             * @param mixed ...$args
             * @return void
             */
            public function add_password_setting(...$args): void
            {
                $this->password[] = $args;
            }

            /**
             * @param mixed ...$args
             * @return void
             */
            public function add_dropdown_setting(...$args): void
            {
                $this->dropdown[] = $args;
            }
        };

        $event = new GenericEvent($settings);
        Plugin::getSettings($event);

        $this->assertCount(1, $textSettings, 'Expected one text setting to be registered');
        $this->assertCount(1, $passwordSettings, 'Expected one password setting to be registered');
        $this->assertCount(1, $dropdownSettings, 'Expected one dropdown setting to be registered');
    }

    /**
     * Test that getActivate calls stopPropagation when category matches.
     *
     * Uses an anonymous class as the service subject to avoid mocking vendor classes.
     *
     * @return void
     */
    public function testGetActivateStopsPropagationOnMatch(): void
    {
        $serviceClass = new class {
            /**
             * @return int
             */
            public function getId(): int
            {
                return 42;
            }

            /**
             * @return string
             */
            public function getIp(): string
            {
                return '10.0.0.1';
            }
        };

        $event = new GenericEvent($serviceClass, [
            'category' => 'WHMSONIC_TYPE',
            'field1' => 'Monthly License',
            'email' => 'test@example.com',
        ]);

        Plugin::getActivate($event);

        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * Test that getActivate does NOT stop propagation when category does not match.
     *
     * @return void
     */
    public function testGetActivateDoesNotStopPropagationOnMismatch(): void
    {
        $serviceClass = new class {
            /**
             * @return int
             */
            public function getId(): int
            {
                return 1;
            }

            /**
             * @return string
             */
            public function getIp(): string
            {
                return '10.0.0.1';
            }
        };

        $event = new GenericEvent($serviceClass, [
            'category' => 'SOME_OTHER_TYPE',
            'field1' => 'Monthly License',
            'email' => 'test@example.com',
        ]);

        Plugin::getActivate($event);

        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * Test that the source file for Plugin class exists.
     *
     * @return void
     */
    public function testPluginSourceFileExists(): void
    {
        $this->assertNotFalse($this->reflection->getFileName());
        $this->assertFileExists($this->reflection->getFileName());
    }

    /**
     * Test that the Plugin class uses Symfony GenericEvent in its imports.
     *
     * @return void
     */
    public function testPluginUsesGenericEvent(): void
    {
        $source = file_get_contents($this->reflection->getFileName());
        $this->assertStringContainsString(
            'use Symfony\\Component\\EventDispatcher\\GenericEvent',
            $source
        );
    }
}
