# A powerful, easy to configure uptime monitor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-uptime-monitor/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-uptime-monitor)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/2551eaa3-34df-49e0-a170-709b96f2ac3e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/2551eaa3-34df-49e0-a170-709b96f2ac3e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-uptime-monitor)
[![StyleCI](https://styleci.io/repos/67774357/shield)](https://styleci.io/repos/67774357)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment you are required to send us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Installation

You can install the package via composer:

``` bash
composer require spatie/laravel-uptime-monitor
```

```bash
php artisan vendor:publish --provider="Spatie\UptimeMonitor\UptimeMonitorServiceProvider"
```

## Usage

``` php
$site = new Spatie\UptimeMonitor();
echo $site->echoPhrase('Hello, Spatie!');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
