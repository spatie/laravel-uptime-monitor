<?php

namespace Spatie\UptimeMonitor\Test\Integration;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Spatie\UptimeMonitor\UptimeMonitorServiceProvider;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class PublishedMigrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [UptimeMonitorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app->useDatabasePath(sys_get_temp_dir().'/uptime-monitor-'.bin2hex(random_bytes(8)));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(database_path());

        parent::tearDown();
    }

    public static function aliasConfigurations(): array
    {
        return [
            'default aliases' => [true],
            'without aliases' => [false],
        ];
    }

    #[Test]
    #[DataProvider('aliasConfigurations')]
    public function the_published_migration_can_be_migrated_and_rolled_back(bool $withAliases): void
    {
        if (! $withAliases) {
            AliasLoader::getInstance()->setAliases([]);
        }

        $this->assertSame($withAliases, class_exists('Schema'));

        $this->artisan('vendor:publish', [
            '--provider' => UptimeMonitorServiceProvider::class,
            '--tag' => 'migrations',
        ])->assertExitCode(0);

        $this->assertCount(1, glob(database_path('migrations/*_create_monitors_table.php')));

        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);

        $this->assertTrue(Schema::hasTable('monitors'));

        DB::table('monitors')->insert(['url' => 'https://example.com']);

        $this->assertDatabaseHas('monitors', [
            'url' => 'https://example.com',
            'uptime_check_enabled' => true,
            'uptime_status' => 'not yet checked',
            'certificate_check_enabled' => false,
            'certificate_status' => 'not yet checked',
        ]);

        $this->artisan('migrate:rollback', ['--force' => true])->assertExitCode(0);

        $this->assertFalse(Schema::hasTable('monitors'));
    }
}
