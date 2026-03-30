---
name: whmsonic-api-call
description: Adds a new WHMSonic reseller API function to `src/whmsonic.inc.php` following the exact cURL POST pattern used by existing functions. Use when asked to 'add API call', 'new whmsonic function', 'call whmsonic endpoint', or add activate/suspend/terminate/list/verify operations. Generates the full cURL block with error handling and 'Complete' response check, plus a matching existence test. Do NOT use for modifying `src/Plugin.php` event handlers or hook registration.
---
# whmsonic-api-call

## Critical

- Never interpolate raw user input into `$fieldstring` — all values come from trusted function parameters or runtime constants (`WHMSONIC_USERNAME`, `WHMSONIC_PASSWORD`).
- Never store or log `WHMSONIC_USERNAME` / `WHMSONIC_PASSWORD` — they are runtime constants only.
- The `spamprotection` token `654a65z4a9AAQZloqe` is required in every WHMSonic API call. Do not omit it.
- All action functions must set `$resellerusername = WHMSONIC_USERNAME` and `$resellerpassword = WHMSONIC_PASSWORD` — never hardcode credentials.
- Standard action functions return `'success'` on `$retval == 'Complete'`, or `"<br>$retval"` on failure. Deviate only for list/verify which have their own response shapes.

## Instructions

1. **Identify the endpoint and command.** Most operations POST to `http://www.whmsonic.com/api/action.php?` with a `cmd=<action>` field. The list operation uses `http://www.whmsonic.com/api/list.php?` with `cmd=list`. The verify operation uses `http://www.whmsonic.com/verify2.php` with no `cmd`.
   - Verify the cmd name matches the operation (e.g. `cmd=suspend`, `cmd=terminate`).

2. **Write the PHPDoc block** above the function in `src/whmsonic.inc.php`. Follow the existing style exactly:
   ```php
   /**
    * whmsonic_<action>()
    * <one-line description>
    *
    * @param string $licenseip ip address to act on
    * @return string "success" if ok , otherwise it returns the error
    */
   ```

3. **Write the function body** using this exact skeleton for standard action functions:
   ```php
   function whmsonic_<action>($licenseip)
   {
       $resellerusername = WHMSONIC_USERNAME;
       $resellerpassword = WHMSONIC_PASSWORD;

       $url = 'http://www.whmsonic.com/api/action.php?';
       $fieldstring = "cmd=<action>&username=$resellerusername&upass=$resellerpassword&licenseip=$licenseip&spamprotection=654a65z4a9AAQZloqe";
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_TIMEOUT, 59);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       $retval = curl_exec($ch);
       if (curl_errno($ch)) {
           $retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
       }
       curl_close($ch);

       if ($retval == 'Complete') {
           $result = 'success';
       } else {
           $result = "<br>$retval";
       }
       return $result;
   }
   ```
   - For extra parameters (like `activate_whmsonic`), add them to the signature and append to `$fieldstring` (e.g. `&license=$license&orderid=$orderid`).
   - Verify the function ends with `return $result;` before proceeding.

4. **Add an existence test** in `tests/WhmsonicFunctionsTest.php` following the existing pattern:
   ```php
   public function testWhmsonicActionFunctionExists(): void
   {
       $this->assertTrue(function_exists('whmsonic_<action>'));
   }
   ```
   - Also add a parameter count test mirroring `testWhmsonicTerminateParameterCount`.
   - Also add to the `$expectedFunctions` array in `testSourceFileContainsAllFunctionDefinitions`.

5. **If the function is called from Plugin.php**, add a namespace-level stub to `tests/stubs.php`:
   ```php
   if (!function_exists('Detain\\MyAdminWhmsonic\\whmsonic_<action>')) {
       function whmsonic_<action>(...$args): string {
           return 'success';
       }
   }
   ```

6. **Run tests** to confirm no regressions:
   ```bash
   vendor/bin/phpunit
   ```
   All tests must pass before the task is complete.

## Examples

**User says:** "add a whmsonic_reactivate function that reactivates a license by IP"

**Actions taken:**
1. Append to `src/whmsonic.inc.php`:
   ```php
   /**
    * whmsonic_reactivate()
    * reactivate a whmsonic license
    *
    * @param string $licenseip ip address to reactivate
    * @return string "success" if ok , otherwise it returns the error
    */
   function whmsonic_reactivate($licenseip)
   {
       $resellerusername = WHMSONIC_USERNAME;
       $resellerpassword = WHMSONIC_PASSWORD;

       $url = 'http://www.whmsonic.com/api/action.php?';
       $fieldstring = "cmd=reactivate&username=$resellerusername&upass=$resellerpassword&licenseip=$licenseip&spamprotection=654a65z4a9AAQZloqe";
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_TIMEOUT, 59);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       $retval = curl_exec($ch);
       if (curl_errno($ch)) {
           $retval = 'CURL Error: '.curl_errno($ch).' - '.curl_error($ch);
       }
       curl_close($ch);

       if ($retval == 'Complete') {
           $result = 'success';
       } else {
           $result = "<br>$retval";
       }
       return $result;
   }
   ```
2. Add `testWhmsonicReactivateFunctionExists` and `testWhmsonicReactivateParameterCount` to `tests/WhmsonicFunctionsTest.php`.
3. Add `'whmsonic_reactivate'` to the `$expectedFunctions` array in `testSourceFileContainsAllFunctionDefinitions`.
4. Run `vendor/bin/phpunit` — all tests pass.

**Result:** New function added, tests green.

## Common Issues

- **`curl_exec` returns `false` instead of error string:** This means cURL failed silently. The `curl_errno($ch)` check catches this — confirm it is present immediately after `curl_exec`.
- **Response is not `'Complete'` but the API call succeeded:** WHMSonic returns `'Complete'` only on success. Any other string (including empty string or HTML) is an error. If you see unexpected responses, `var_dump($retval)` before the `if` check.
- **`WHMSONIC_USERNAME` / `WHMSONIC_PASSWORD` undefined in tests:** Add `define('WHMSONIC_USERNAME', 'test_user')` and `define('WHMSONIC_PASSWORD', 'test_pass')` inside `setUpBeforeClass()` in `tests/WhmsonicFunctionsTest.php`, guarded by `if (!defined(...))`.
- **New function not found by tests:** Confirm `require_once` in `setUpBeforeClass()` loads `dirname(__DIR__) . '/src/whmsonic.inc.php'` — the file must be saved before running tests.
- **`spamprotection` missing causes API rejection:** Every POST to `http://www.whmsonic.com/api/action.php` must include `&spamprotection=654a65z4a9AAQZloqe` in `$fieldstring`. Verify it is present in the string literal.
