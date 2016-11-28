<?php
namespace Spatie\UptimeMonitor\Exceptions;

/**
 * Class InvalidArgument
 * @package Spatie\UptimeMonitor\Exceptions
 */
class InvalidArgument extends \InvalidArgumentException
{
    /**
     * @param $protocol
     */
    public static function unknowProtocol($protocol)
    {
        throw new static("We doesn't know anything about the protocol `{$protocol}");
    }

    public static function checkerAlreadyRegisterd($protocol){
        throw new static("For the Protocol `{$protocol}` is already an Checker registerd.");
    }
}
