<?php
namespace Spatie\UptimeMonitor\Checker;

use Illuminate\Support\Collection;
use Spatie\UptimeMonitor\Exceptions\InvalidArgument;

/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 28.11.16
 * Time: 10:28
 */
class CheckerRepository
{
    /**
     * @var Collection
     */
    protected $protocolsToChecker = [];


    /**
     * @var
     */
    protected static $self;

    /**
     * @param $protocol
     * @param Checker $checker
     */
    public function addChecker($protocol, Checker $checker)
    {
        if (!array_key_exists($protocol, $this->protocolsToChecker)) {
            $this->protocolsToChecker[$protocol] = null;
        }
        if (!empty($this->protocolsToChecker[$protocol])) {
            throw InvalidArgument::checkerAlreadyRegisterd($protocol);
        }
        $this->protocolsToChecker[$protocol] = $checker;
    }

    /**
     * @param null $protocol
     * @return array
     */
    public function getChecker($protocol = null): array
    {
        if ($protocol != null) {
            if (array_key_exists($protocol, $this->protocolsToChecker)) {
                return $this->protocolsToChecker[$protocol];
            } else {
                throw InvalidArgument::unknowProtocol($protocol);
            }
        }
        return $this->protocolsToChecker;
    }

    /**
     * @return CheckerRepository
     */
    public static function get()
    {
        if (!self::$self instanceof CheckerRepository) {
            self::$self = new CheckerRepository();
        }
        return self::$self;
    }
}