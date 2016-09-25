<?php

namespace Spatie\UptimeMonitor\Commands;

use Illuminate\Console\Command;
use Spatie\UptimeMonitor\Helpers\Emoji;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class ListUptimeMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uptime-monitor:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all uptime monitors';

    public function handle()
    {
        $this->listUnhealthySites();
        $this->listHealthySites();
    }

    public function listUnhealthySites()
    {

    }

    public function listHealthySites()
    {
        $this->info('Healthy sites');
        $this->info('============');

        $rows = SiteRepository::healthySites()->map(function (Site $site) {
            $url = $site->url;

            $reachable = ($site->uptime_status === UptimeStatus::UP) ? Emoji::ok() : Emoji::notOk();

            $onlineSince = $site->last_uptime_status_change_on->diffForHumans();

            if ($site->check_ssl_certificate) {
                $sslCertificateFound = Emoji::ok();
                $sslCertificateExpirationDate = $site->ssl_certificate_expiration_date->diffForHumans();
                $sslCertificateIssuer = $site->ssl_certificate_issuer;
            }



            return compact('url', 'reachable', 'onlineSince', 'sslCertificateFound', 'sslCertificateExpirationDate', 'sslCertificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Online since', 'SSL Certifcate', 'SSL Expiration date', 'SSL Issuer'];

        $this->table($titles, $rows);
    }
}
