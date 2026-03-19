<?php

declare(strict_types=1);

namespace Detain\MyAdminWhmsonic\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;

/**
 * Test suite for the procedural functions in whmsonic.inc.php.
 *
 * Since these functions rely on cURL calls to an external API and global
 * constants, the tests focus on static analysis: function existence, parameter
 * signatures, and return type expectations rather than live API calls.
 *
 * @coversNothing
 */
class WhmsonicFunctionsTest extends TestCase
{
    /**
     * Ensure the include file is loaded once for all tests in this class.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        if (!defined('WHMSONIC_USERNAME')) {
            define('WHMSONIC_USERNAME', 'test_user');
        }
        if (!defined('WHMSONIC_PASSWORD')) {
            define('WHMSONIC_PASSWORD', 'test_pass');
        }

        $incFile = dirname(__DIR__) . '/src/whmsonic.inc.php';
        if (file_exists($incFile)) {
            require_once $incFile;
        }
    }

    /**
     * Test that the whmsonic.inc.php source file exists.
     *
     * @return void
     */
    public function testIncludeFileExists(): void
    {
        $incFile = dirname(__DIR__) . '/src/whmsonic.inc.php';
        $this->assertFileExists($incFile);
    }

    /**
     * Test that the activate_whmsonic function is defined.
     *
     * @return void
     */
    public function testActivateWhmsonicFunctionExists(): void
    {
        $this->assertTrue(function_exists('activate_whmsonic'));
    }

    /**
     * Test that the whmsonic_terminate function is defined.
     *
     * @return void
     */
    public function testWhmsonicTerminateFunctionExists(): void
    {
        $this->assertTrue(function_exists('whmsonic_terminate'));
    }

    /**
     * Test that the whmsonic_suspend function is defined.
     *
     * @return void
     */
    public function testWhmsonicSuspendFunctionExists(): void
    {
        $this->assertTrue(function_exists('whmsonic_suspend'));
    }

    /**
     * Test that the whmsonic_unsuspend function is defined.
     *
     * @return void
     */
    public function testWhmsonicUnsuspendFunctionExists(): void
    {
        $this->assertTrue(function_exists('whmsonic_unsuspend'));
    }

    /**
     * Test that the whmsonic_list function is defined.
     *
     * @return void
     */
    public function testWhmsonicListFunctionExists(): void
    {
        $this->assertTrue(function_exists('whmsonic_list'));
    }

    /**
     * Test that the whmsonic_verify function is defined.
     *
     * @return void
     */
    public function testWhmsonicVerifyFunctionExists(): void
    {
        $this->assertTrue(function_exists('whmsonic_verify'));
    }

    /**
     * Test that activate_whmsonic accepts exactly 5 parameters.
     *
     * @return void
     */
    public function testActivateWhmsonicParameterCount(): void
    {
        $ref = new ReflectionFunction('activate_whmsonic');
        $this->assertCount(5, $ref->getParameters());
    }

    /**
     * Test the parameter names of activate_whmsonic.
     *
     * @return void
     */
    public function testActivateWhmsonicParameterNames(): void
    {
        $ref = new ReflectionFunction('activate_whmsonic');
        $params = $ref->getParameters();
        $names = array_map(fn($p) => $p->getName(), $params);
        $this->assertSame(['licenseip', 'license', 'orderid', 'clientName', 'clientEmail'], $names);
    }

    /**
     * Test that activate_whmsonic has no optional parameters.
     *
     * @return void
     */
    public function testActivateWhmsonicAllParametersRequired(): void
    {
        $ref = new ReflectionFunction('activate_whmsonic');
        foreach ($ref->getParameters() as $param) {
            $this->assertFalse(
                $param->isOptional(),
                "Parameter \${$param->getName()} should be required"
            );
        }
    }

    /**
     * Test that whmsonic_terminate accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testWhmsonicTerminateParameterCount(): void
    {
        $ref = new ReflectionFunction('whmsonic_terminate');
        $this->assertCount(1, $ref->getParameters());
    }

    /**
     * Test the parameter name of whmsonic_terminate.
     *
     * @return void
     */
    public function testWhmsonicTerminateParameterName(): void
    {
        $ref = new ReflectionFunction('whmsonic_terminate');
        $this->assertSame('licenseip', $ref->getParameters()[0]->getName());
    }

    /**
     * Test that whmsonic_suspend accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testWhmsonicSuspendParameterCount(): void
    {
        $ref = new ReflectionFunction('whmsonic_suspend');
        $this->assertCount(1, $ref->getParameters());
    }

    /**
     * Test the parameter name of whmsonic_suspend.
     *
     * @return void
     */
    public function testWhmsonicSuspendParameterName(): void
    {
        $ref = new ReflectionFunction('whmsonic_suspend');
        $this->assertSame('licenseip', $ref->getParameters()[0]->getName());
    }

    /**
     * Test that whmsonic_unsuspend accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testWhmsonicUnsuspendParameterCount(): void
    {
        $ref = new ReflectionFunction('whmsonic_unsuspend');
        $this->assertCount(1, $ref->getParameters());
    }

    /**
     * Test the parameter name of whmsonic_unsuspend.
     *
     * @return void
     */
    public function testWhmsonicUnsuspendParameterName(): void
    {
        $ref = new ReflectionFunction('whmsonic_unsuspend');
        $this->assertSame('licenseip', $ref->getParameters()[0]->getName());
    }

    /**
     * Test that whmsonic_list accepts exactly 1 parameter with a default value.
     *
     * @return void
     */
    public function testWhmsonicListParameterCount(): void
    {
        $ref = new ReflectionFunction('whmsonic_list');
        $this->assertCount(1, $ref->getParameters());
    }

    /**
     * Test that whmsonic_list has an optional parameter with default value of 4.
     *
     * @return void
     */
    public function testWhmsonicListDefaultParameterValue(): void
    {
        $ref = new ReflectionFunction('whmsonic_list');
        $param = $ref->getParameters()[0];
        $this->assertTrue($param->isOptional());
        $this->assertSame(4, $param->getDefaultValue());
    }

    /**
     * Test the parameter name of whmsonic_list.
     *
     * @return void
     */
    public function testWhmsonicListParameterName(): void
    {
        $ref = new ReflectionFunction('whmsonic_list');
        $this->assertSame('type', $ref->getParameters()[0]->getName());
    }

    /**
     * Test that whmsonic_verify accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testWhmsonicVerifyParameterCount(): void
    {
        $ref = new ReflectionFunction('whmsonic_verify');
        $this->assertCount(1, $ref->getParameters());
    }

    /**
     * Test the parameter name of whmsonic_verify.
     *
     * @return void
     */
    public function testWhmsonicVerifyParameterName(): void
    {
        $ref = new ReflectionFunction('whmsonic_verify');
        $this->assertSame('clientserverIP', $ref->getParameters()[0]->getName());
    }

    /**
     * Test that the source file contains all expected function definitions.
     *
     * @return void
     */
    public function testSourceFileContainsAllFunctionDefinitions(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $expectedFunctions = [
            'activate_whmsonic',
            'whmsonic_terminate',
            'whmsonic_suspend',
            'whmsonic_unsuspend',
            'whmsonic_list',
            'whmsonic_verify',
        ];
        foreach ($expectedFunctions as $funcName) {
            $this->assertStringContainsString(
                "function {$funcName}(",
                $source,
                "Expected function {$funcName} to be defined in whmsonic.inc.php"
            );
        }
    }

    /**
     * Test that all API-calling functions use cURL internally.
     *
     * @return void
     */
    public function testSourceFileUsesCurl(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $this->assertStringContainsString('curl_init', $source);
        $this->assertStringContainsString('curl_exec', $source);
        $this->assertStringContainsString('curl_close', $source);
    }

    /**
     * Test that the source file references the WHMSonic API endpoint.
     *
     * @return void
     */
    public function testSourceFileReferencesApiEndpoint(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $this->assertStringContainsString('whmsonic.com/api/', $source);
    }

    /**
     * Test that activate_whmsonic uses the correct API command.
     *
     * @return void
     */
    public function testActivateWhmsonicUsesCreateCommand(): void
    {
        $ref = new ReflectionFunction('activate_whmsonic');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('cmd=create', $funcSource);
    }

    /**
     * Test that whmsonic_terminate uses the correct API command.
     *
     * @return void
     */
    public function testWhmsonicTerminateUsesTerminateCommand(): void
    {
        $ref = new ReflectionFunction('whmsonic_terminate');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('cmd=terminate', $funcSource);
    }

    /**
     * Test that whmsonic_suspend uses the correct API command.
     *
     * @return void
     */
    public function testWhmsonicSuspendUsesSuspendCommand(): void
    {
        $ref = new ReflectionFunction('whmsonic_suspend');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('cmd=suspend', $funcSource);
    }

    /**
     * Test that whmsonic_unsuspend uses the correct API command.
     *
     * @return void
     */
    public function testWhmsonicUnsuspendUsesUnsuspendCommand(): void
    {
        $ref = new ReflectionFunction('whmsonic_unsuspend');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('cmd=unsuspend', $funcSource);
    }

    /**
     * Test that whmsonic_list uses the list API endpoint.
     *
     * @return void
     */
    public function testWhmsonicListUsesListEndpoint(): void
    {
        $ref = new ReflectionFunction('whmsonic_list');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('list.php', $funcSource);
        $this->assertStringContainsString('cmd=list', $funcSource);
    }

    /**
     * Test that whmsonic_verify uses the verify endpoint.
     *
     * @return void
     */
    public function testWhmsonicVerifyUsesVerifyEndpoint(): void
    {
        $ref = new ReflectionFunction('whmsonic_verify');
        $source = file_get_contents($ref->getFileName());
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();
        $lines = array_slice(
            explode("\n", $source),
            $startLine - 1,
            $endLine - $startLine + 1
        );
        $funcSource = implode("\n", $lines);
        $this->assertStringContainsString('verify2.php', $funcSource);
    }

    /**
     * Test that all action functions reference the WHMSONIC_USERNAME constant.
     *
     * @return void
     */
    public function testActionFunctionsReferenceUsernameConstant(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $this->assertGreaterThan(
            1,
            substr_count($source, 'WHMSONIC_USERNAME'),
            'WHMSONIC_USERNAME should be referenced multiple times'
        );
    }

    /**
     * Test that all action functions reference the WHMSONIC_PASSWORD constant.
     *
     * @return void
     */
    public function testActionFunctionsReferencePasswordConstant(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $this->assertGreaterThan(
            1,
            substr_count($source, 'WHMSONIC_PASSWORD'),
            'WHMSONIC_PASSWORD should be referenced multiple times'
        );
    }

    /**
     * Test that cURL error handling is present in all API functions.
     *
     * @return void
     */
    public function testCurlErrorHandlingPresent(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $curlErrnoCount = substr_count($source, 'curl_errno');
        $curlErrorCount = substr_count($source, 'curl_error');
        // Each of the 6 functions should have error handling
        $this->assertGreaterThanOrEqual(6, $curlErrnoCount);
        $this->assertGreaterThanOrEqual(6, $curlErrorCount);
    }

    /**
     * Test that no function uses deprecated cURL options.
     *
     * @return void
     */
    public function testNoDeprecatedCurlOptions(): void
    {
        $source = file_get_contents(dirname(__DIR__) . '/src/whmsonic.inc.php');
        $this->assertStringNotContainsString('CURLOPT_SAFE_UPLOAD', $source);
        $this->assertStringNotContainsString('CURLOPT_CLOSEPOLICY', $source);
    }

    /**
     * Test that the number of required parameters for each IP-based function is exactly 1.
     *
     * @return void
     */
    public function testIpBasedFunctionsRequireExactlyOneParameter(): void
    {
        $ipFunctions = [
            'whmsonic_terminate',
            'whmsonic_suspend',
            'whmsonic_unsuspend',
            'whmsonic_verify',
        ];
        foreach ($ipFunctions as $funcName) {
            $ref = new ReflectionFunction($funcName);
            $this->assertSame(
                1,
                $ref->getNumberOfRequiredParameters(),
                "{$funcName} should require exactly 1 parameter"
            );
        }
    }
}
