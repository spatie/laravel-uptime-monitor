<?php

namespace Spatie\UptimeMonitor;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Checker\Checker;
use Spatie\UptimeMonitor\Checker\CheckerRepository;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorCollection extends Collection
{
    /**
     * @return static
     */
    public function sortByHost()
    {
        return $this->sortBy(function (Monitor $monitor) {
            return $monitor->url->getHost();
        });
    }

    public function checkUptime()
    {
        $this->resetItemKeys();
        foreach (CheckerRepository::get()->getChecker() as $protocol => $checker) {
            /*
             * @var $checker Checker
             */
            $checker->check($this->filter(function ($value) use ($protocol) {
                if (! ends_with($protocol, '*')) {
                    $protocol = $protocol.'*';
                }

                return str_is($protocol, $value->url->getScheme());
            }));
        }
    }

    /**
     * In order to make use of Guzzle promises we have to make sure the
     * keys of the collection are in a consecutive order without gaps.
     */
    public function resetItemKeys()
    {
        $this->items = $this->values()->all();
    }

    public function getMonitorAtIndex(int $index): Monitor
    {
        return $this->items[$index];
    }
}
