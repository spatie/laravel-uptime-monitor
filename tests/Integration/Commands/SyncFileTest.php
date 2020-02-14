<?php

namespace Spatie\UptimeMonitor\Test\Integration\Commands;

use Artisan;
use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Test\TestCase;

class SyncFileTest extends TestCase
{
    protected $stubsDirectory = __DIR__.'/../stubs/';

    public function setUp(): void
    {
        parent::setUp();

        Monitor::create([
            'url' => 'https://www.example.com',
            'uptime_check_enabled' => false,
            'certificate_check_enabled' => true,
        ]);
    }

    /** @test */
    public function it_can_create_monitors()
    {
        Artisan::call('monitor:sync-file', ['path' => $this->stubsDirectory.'sync-file-original.json']);

        $this->seeInConsoleOutput('Synced 2 monitor(s) to database');

        $importMonitor1 = Monitor::where('url', 'https://www.https-example2.com')->first();
        $importMonitor2 = Monitor::where('url', 'http://www.http-example2.com')->first();

        $this->assertTrue($importMonitor1->uptime_check_enabled);
        $this->assertTrue($importMonitor1->certificate_check_enabled);
        $this->assertFalse($importMonitor2->uptime_check_enabled);
        $this->assertFalse($importMonitor2->certificate_check_enabled);
    }

    /** @test */
    public function it_throws_an_exception_for_invalid_urls()
    {
        $this->expectException(CannotSaveMonitor::class);

        Artisan::call('monitor:sync-file', ['path' => $this->stubsDirectory.'sync-file-invalid.json']);

        $this->assertEmpty(Monitor::where('url', 'www.example.com'));
    }

    /** @test */
    public function it_can_update_existing_monitors()
    {
        Artisan::call('monitor:sync-file', ['path' => $this->stubsDirectory.'sync-file-update.json']);

        $this->seeInConsoleOutput('Synced 1 monitor(s) to database');

        $updatedMonitor = Monitor::where('url', 'https://www.example.com')->first();

        $this->assertFalse($updatedMonitor->uptime_check_enabled);
        $this->assertTrue($updatedMonitor->certificate_check_enabled);
    }

    /** @test */
    public function it_can_delete_monitors_not_found_in_file()
    {
        Artisan::call('monitor:sync-file', [
            'path' => $this->stubsDirectory.'sync-file-original.json',
            '--delete-missing' => true,
        ]);

        $deletedMonitor = Monitor::where('url', 'https://www.example.com')->first();

        $this->seeInConsoleOutput('Deleted monitor for `https://www.example.com`');

        $this->assertEmpty($deletedMonitor);
    }
}
