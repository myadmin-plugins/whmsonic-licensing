# MyAdmin WHMSonic Licensing Plugin

Composer plugin package that integrates WHMSonic shoutcast/icecast streaming licenses into the MyAdmin billing panel via the WHMSonic reseller API.

## Commands

```bash
composer install                    # install deps
vendor/bin/phpunit                  # run all tests
php bin/whmsonic_licenses.php       # list licenses via CLI
```

## Architecture

- **Plugin entry**: `src/Plugin.php` · namespace `Detain\MyAdminWhmsonic` · PSR-4 → `src/`
- **API functions**: `src/whmsonic.inc.php` · procedural · loaded via `function_requirements()`
- **CLI**: `bin/whmsonic_licenses.php` · requires `../../../../include/functions.inc.php`
- **Tests**: `tests/` · bootstrap `tests/bootstrap.php` · stubs `tests/stubs.php` · config `phpunit.xml.dist`
- **Event system**: `Symfony\Component\EventDispatcher\GenericEvent` · hooks registered in `Plugin::getHooks()`
- **CI/CD**: `.github/` contains workflows for automated testing and deployment pipelines
- **IDE config**: `.idea/` contains inspectionProfiles, deployment.xml, and encodings.xml for project settings

## Plugin Hook Pattern

```php
public static function getHooks(): array {
    return [
        'function.requirements' => [__CLASS__, 'getRequirements'],
        self::$module.'.activate' => [__CLASS__, 'getActivate'],
    ];
}

public static function getActivate(GenericEvent $event): void {
    if ($event['category'] == get_service_define('WHMSONIC')) {
        $serviceClass = $event->getSubject();
        myadmin_log(self::$module, 'info', 'message', __LINE__, __FILE__, self::$module, $serviceClass->getId());
        // ... do work ...
        $event->stopPropagation();
    }
}
```

## WHMSonic API Pattern

All API calls in `src/whmsonic.inc.php` POST to `http://www.whmsonic.com/api/action.php` with `WHMSONIC_USERNAME` / `WHMSONIC_PASSWORD` constants. Response `'Complete'` = success, anything else = error string.

```php
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
```

## Test Stub Pattern

MyAdmin globals are stubbed in `tests/stubs.php` under `namespace Detain\MyAdminWhmsonic` using `function_exists` guards. Add new stubs there — never mock with PHPUnit mocks for global functions.

```php
if (!function_exists('Detain\\MyAdminWhmsonic\\myadmin_log')) {
    function myadmin_log(...$args): void {}
}
```

## Conventions

- Commit messages: lowercase, descriptive (`fix whmsonic suspend`, `add verify handler`)
- Never commit credentials — `WHMSONIC_USERNAME`/`WHMSONIC_PASSWORD` are runtime constants
- `myadmin_log(self::$module, $level, $message, __LINE__, __FILE__, self::$module, $id)` for all logging
- Available functions: `activate_whmsonic`, `whmsonic_terminate`, `whmsonic_suspend`, `whmsonic_unsuspend`, `whmsonic_list`, `whmsonic_verify`
- License types: `1`=yearly, `2`=monthly, `3`=lifetime, `4`=all (default in `whmsonic_list`)

## Plugin contract harness

This package is on the shared contract harness from `detain/myadmin-plugin-installer`.
`tests/ContractTest.php` is **generated** — run `composer myadmin:scaffold-tests` (add
`--force --write` to re-emit it), never hand-edit it.

The harness **executes** the plugin: it defines the bare constants the class body references
and then calls `getHooks()`, `getSettings()`, `getMenu()`, `apiRegister()` and — for
`type=service` packages — the activate/deactivate/change-ip/queue handlers, for real.

**So do not write reflection-only tests for the plugin class.** Asserting a handler exists,
is public, is static and takes one parameter passes whether or not the handler works; three
production bugs in this fleet were sitting behind assertions of exactly that shape. Older
guidance in this repo that says those methods must not be called predates the harness.

The harness is **additive**: it runs alongside this package's existing tests, and nothing is
deleted to make room for it. Run the whole suite, never `--filter ContractTest` alone — the
contract class primes constants and calls `register_module()`, neither of which can be undone.

See the `plugin-contract-tests` skill for the full workflow, and `docs/testing-harness.md` in
the installer.

## Before Committing

```bash
caliber refresh && git add CLAUDE.md .claude/ CALIBER_LEARNINGS.md 2>/dev/null
```

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md .agents/ .opencode/ 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
