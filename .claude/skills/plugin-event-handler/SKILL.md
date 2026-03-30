---
name: plugin-event-handler
description: Adds a new Symfony GenericEvent hook handler to src/Plugin.php following the existing pattern. Use when user says 'add hook', 'new event handler', 'handle event', 'add plugin action', or needs to wire a new lifecycle event (activate, suspend, terminate, change IP, menu). Registers in getHooks() and implements the handler with category check and stopPropagation(). Do NOT use for adding procedural API functions to src/whmsonic.inc.php.
---
# plugin-event-handler

## Critical

- All handler methods MUST be `public static function` — never instance methods
- ALWAYS guard with `if ($event['category'] == get_service_define('WHMSONIC'))` before acting, then call `$event->stopPropagation()` as the last line inside the guard
- NEVER skip `$event->stopPropagation()` — other plugins share the same event; missing it causes double-execution
- Log every handler entry with the 7-arg form: `myadmin_log(self::$module, 'info', 'message', __LINE__, __FILE__, self::$module, $serviceClass->getId())`
- NEVER commit `WHMSONIC_USERNAME` / `WHMSONIC_PASSWORD` literals — they are runtime constants

## Instructions

1. **Choose the event name.** Common patterns already in the codebase:
   - `self::$module.'.activate'` / `self::$module.'.reactivate'`
   - `self::$module.'.suspend'` / `self::$module.'.unsuspend'`
   - `self::$module.'.terminate'`
   - `self::$module.'.change_ip'`
   - `'menu'` (no module prefix)
   Verify the event name is not already registered in `getHooks()` before adding.

2. **Register in `getHooks()`.** Open `src/Plugin.php` and add an entry to the returned array:
   ```php
   self::$module.'.suspend' => [__CLASS__, 'getSuspend'],
   ```
   Use `[__CLASS__, 'MethodName']` — never a closure or string.

3. **Implement the handler method** directly below the last existing handler, following this exact skeleton:
   ```php
   /**
    * @param \Symfony\Component\EventDispatcher\GenericEvent $event
    */
   public static function getSuspend(GenericEvent $event)
   {
       if ($event['category'] == get_service_define('WHMSONIC')) {
           $serviceClass = $event->getSubject();
           myadmin_log(self::$module, 'info', 'WHMSonic Suspend', __LINE__, __FILE__, self::$module, $serviceClass->getId());
           function_requirements('whmsonic_suspend');
           whmsonic_suspend($serviceClass->getIp(), $serviceClass->getId());
           $event->stopPropagation();
       }
   }
   ```
   - `$event->getSubject()` returns the service object; call `getId()`, `getIp()`, `getCustid()` as needed
   - Use `function_requirements('whmsonic_fn_name')` to lazy-load before calling any `src/whmsonic.inc.php` function
   - For error results, set `$event['status'] = 'error'` and `$event['status_text'] = '...'` before `stopPropagation()`

4. **Verify the method signature** matches every other handler: one parameter typed `GenericEvent $event`, no return type declared (existing handlers omit it).

5. **Run tests** to confirm nothing is broken:
   ```bash
   vendor/bin/phpunit
   ```
   The existing `PluginTest::testGetHooksMethodsExist` will fail if the method name in `getHooks()` doesn't match the actual method — fix the typo if it does.

## Examples

**User says:** "Add a suspend event handler"

**Actions taken:**
1. Add to `getHooks()` in `src/Plugin.php`:
   ```php
   self::$module.'.suspend' => [__CLASS__, 'getSuspend'],
   ```
2. Add method to `src/Plugin.php`:
   ```php
   public static function getSuspend(GenericEvent $event)
   {
       if ($event['category'] == get_service_define('WHMSONIC')) {
           $serviceClass = $event->getSubject();
           myadmin_log(self::$module, 'info', 'WHMSonic Suspend', __LINE__, __FILE__, self::$module, $serviceClass->getId());
           function_requirements('whmsonic_suspend');
           whmsonic_suspend($serviceClass->getIp(), $serviceClass->getId());
           $event->stopPropagation();
       }
   }
   ```
3. Run `vendor/bin/phpunit` — all tests pass.

**Result:** Suspend lifecycle event is now handled; other plugins' suspend handlers are not invoked for WHMSONIC services.

## Common Issues

- **`testGetHooksMethodsExist` fails:** The method name string in `getHooks()` doesn't match the actual method name. Check spelling — e.g. `'getSuspend'` vs `'getSuspended'`.
- **Handler fires but also triggers another plugin's handler:** `$event->stopPropagation()` is missing or placed outside the `if` guard. It must be the last statement *inside* the `if` block.
- **`function_requirements` call fails / function not found:** The function name passed must match exactly what's registered in `getRequirements()` via `$loader->add_requirement()` or `$loader->add_page_requirement()`. Check `src/Plugin.php::getRequirements()` for the registered names.
- **`get_service_define('WHMSONIC')` returns null in tests:** The stub for `get_service_define` in `tests/stubs.php` may not cover the `WHMSONIC` key. Add a guard stub there:
  ```php
  if (!function_exists('Detain\\MyAdminWhmsonic\\get_service_define')) {
      function get_service_define(string $key): string { return $key.'_TYPE'; }
  }
  ```
- **Test for `stopPropagation` fails on mismatch case:** The `if` guard is missing — the handler is calling `stopPropagation()` unconditionally instead of only when `$event['category']` matches.