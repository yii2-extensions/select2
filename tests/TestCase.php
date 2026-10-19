<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Tests;

use Yii;
use yii\helpers\FileHelper;
use yii\web\Application;

use function bin2hex;
use function dirname;
use function mkdir;
use function random_bytes;
use function sys_get_temp_dir;

/**
 * Provides a web application with an isolated asset directory for each test.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private string $runtime = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockApplication();
    }

    protected function tearDown(): void
    {
        Yii::$app = null;

        FileHelper::removeDirectory($this->runtime);

        parent::tearDown();
    }

    protected function mockApplication(): void
    {
        $this->runtime = sys_get_temp_dir() . '/select2-tests-' . bin2hex(random_bytes(6));

        mkdir("{$this->runtime}/assets", 0777, true);

        new Application(
            [
                'id' => 'select2-tests',
                'basePath' => dirname(__DIR__),
                'vendorPath' => dirname(__DIR__) . '/vendor',
                'runtimePath' => $this->runtime,
                'language' => 'en-US',
                'aliases' => [
                    '@bower' => dirname(__DIR__) . '/vendor/bower-asset',
                    '@npm' => dirname(__DIR__) . '/node_modules',
                ],
                'components' => [
                    'assetManager' => [
                        'basePath' => $this->runtime . '/assets',
                        'baseUrl' => '/assets',
                    ],
                    'request' => [
                        'cookieValidationKey' => 'select2-tests',
                        'scriptFile' => __FILE__,
                        'scriptUrl' => '/index.php',
                    ],
                ],
            ],
        );
    }
}
