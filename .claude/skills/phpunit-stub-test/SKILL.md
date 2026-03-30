---
name: phpunit-stub-test
description: Adds PHPUnit test cases for this plugin using the stub pattern from tests/stubs.php and tests/bootstrap.php. Use when user says 'add test', 'write tests', 'test this function', or needs coverage for src/Plugin.php or src/whmsonic.inc.php. Adds global function stubs under namespace Detain\MyAdminWhmsonic using function_exists guards, then writes test class under Detain\MyAdminWhmsonic\Tests namespace. Do NOT use for PHPUnit mocks of MyAdmin global functions or for integration tests requiring a live WHMSonic API.
---
# PHPUnit Stub Test

## Critical

- **Never use PHPUnit mocks for MyAdmin global functions** (`myadmin_log`, `get_service_define`, `function_requirements`, `get_module_settings`, `_`). Always add namespace-level stubs to `tests/stubs.php` instead.
- All stubs must live inside `namespace Detain\MyAdminWhmsonic { }` in `tests/stubs.php` and be guarded with `function_exists('Detain\\MyAdminWhmsonic\\func_name')`.
- Do NOT make live cURL calls in tests. For `src/whmsonic.inc.php` functions, test parameter signatures and source analysis via `ReflectionFunction` — not execution.
- Constants `WHMSONIC_USERNAME` / `WHMSONIC_PASSWORD` must be defined in `setUpBeforeClass()` with `if (!defined(...))` guards before `require_once`-ing `src/whmsonic.inc.php`.

## Instructions

1. **Identify what global functions the new code calls.** Read the target source file to find any calls to `myadmin_log`, `get_service_define`, `function_requirements`, `get_module_settings`, `_`, or any `whmsonic_*` function. Verify each against existing stubs in `tests/stubs.php` before proceeding.

2. **Add missing stubs to `tests/stubs.php`.** For each missing global function, append inside the `namespace Detain\MyAdminWhmsonic { }` block:
   ```php
   if (!function_exists('Detain\\MyAdminWhmsonic\\my_new_func')) {
       function my_new_func(...$args): void {}
   }
   ```
   Return a typed value (e.g., `string`, `array`) only if the calling code uses the return value. Verify `tests/bootstrap.php` still loads `stubs.php` before `vendor/autoload.php` — do not modify bootstrap unless a new require is needed.

3. **Create the test class file** under `tests/` with filename `MyFeatureTest.php`. Use this exact header:
   ```php
   <?php
   declare(strict_types=1);
   namespace Detain\MyAdminWhmsonic\Tests;
   use PHPUnit\Framework\TestCase;
   use ReflectionClass;          // for Plugin tests
   use ReflectionFunction;       // for whmsonic.inc.php function tests
   use Symfony\Component\EventDispatcher\GenericEvent; // for event handler tests
   ```

4. **For `src/Plugin.php` tests:** Use `ReflectionClass` for structural assertions. Fire event handlers by constructing `new GenericEvent($subject, $args)` where `$subject` is an anonymous class with the required methods (`getId()`, `getIp()`):
   ```php
   $serviceClass = new class {
       public function getId(): int { return 42; }
       public function getIp(): string { return '10.0.0.1'; }
   };
   $event = new GenericEvent($serviceClass, ['category' => 'WHMSONIC_TYPE']);
   Plugin::getActivate($event);
   $this->assertTrue($event->isPropagationStopped());
   ```
   Assert `isPropagationStopped()` for matching category and `assertFalse` for non-matching category.

5. **For `src/whmsonic.inc.php` tests:** Define constants and `require_once` the file in `setUpBeforeClass()`, then use `ReflectionFunction` for parameter count/name assertions and `file_get_contents(dirname(__DIR__).'/src/whmsonic.inc.php')` + `assertStringContainsString` for API command verification. Do not call the actual functions.

6. **Run tests:** `vendor/bin/phpunit` from the package root. All tests must pass before considering work complete.

## Examples

**User says:** "Add a test for the new `whmsonic_reboot` function in `src/whmsonic.inc.php`."

**Actions taken:**
1. Read `src/whmsonic.inc.php` — find `whmsonic_reboot($licenseip)` calls `myadmin_log` and `curl_*`.
2. `myadmin_log` stub already exists in `tests/stubs.php` — no change needed.
3. Create `tests/WhmsonicRebootTest.php`:
   ```php
   <?php
   declare(strict_types=1);
   namespace Detain\MyAdminWhmsonic\Tests;
   use PHPUnit\Framework\TestCase;
   use ReflectionFunction;
   class WhmsonicRebootTest extends TestCase {
       public static function setUpBeforeClass(): void {
           if (!defined('WHMSONIC_USERNAME')) define('WHMSONIC_USERNAME', 'test_user');
           if (!defined('WHMSONIC_PASSWORD')) define('WHMSONIC_PASSWORD', 'test_pass');
           require_once dirname(__DIR__) . '/src/whmsonic.inc.php';
       }
       public function testWhmsonicRebootExists(): void {
           $this->assertTrue(function_exists('whmsonic_reboot'));
       }
       public function testWhmsonicRebootParameterName(): void {
           $ref = new ReflectionFunction('whmsonic_reboot');
           $this->assertSame('licenseip', $ref->getParameters()[0]->getName());
       }
       public function testWhmsonicRebootUsesRebootCommand(): void {
           $ref = new ReflectionFunction('whmsonic_reboot');
           $lines = array_slice(explode("\n", file_get_contents($ref->getFileName())),
               $ref->getStartLine() - 1, $ref->getEndLine() - $ref->getStartLine() + 1);
           $this->assertStringContainsString('cmd=reboot', implode("\n", $lines));
       }
   }
   ```
4. Run `vendor/bin/phpunit` — all pass.

**Result:** New test class follows exact patterns from `WhmsonicFunctionsTest.php`, no mocks used.

## Common Issues

- **"Call to undefined function Detain\\MyAdminWhmsonic\\myadmin_log"**: The stub is missing or in the wrong namespace block. Open `tests/stubs.php` and confirm the function is declared inside `namespace Detain\MyAdminWhmsonic { }`, not global namespace.
- **"Cannot redeclare function"**: You called `require_once src/whmsonic.inc.php` more than once across test classes in the same process. Add `if (function_exists('activate_whmsonic')) return;` at the top of the include, or use `require_once` consistently — never `require`.
- **"Constant WHMSONIC_USERNAME already defined"**: Always guard with `if (!defined('WHMSONIC_USERNAME'))` in `setUpBeforeClass()`.
- **Tests pass locally but fail in CI (function not found)**: `tests/bootstrap.php` must load `stubs.php` before `vendor/autoload.php`. Confirm line order: `require_once __DIR__ . '/stubs.php';` then `require_once dirname(__DIR__) . '/vendor/autoload.php';`.
- **`$event->isPropagationStopped()` returns false unexpectedly**: The anonymous `$subject` class is missing a method called by the handler (e.g., `getId()`). Read `src/Plugin.php` to check what methods the handler calls on `$event->getSubject()`.