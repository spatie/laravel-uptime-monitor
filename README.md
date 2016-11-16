# DO NOT USE (yet)

This code is pre-alpha quality. Unless you want to be bitten by a thousand bugs, refrain from using it.

# A powerful, easy to configure uptime monitor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-uptime-monitor/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-uptime-monitor)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/2551eaa3-34df-49e0-a170-709b96f2ac3e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/2551eaa3-34df-49e0-a170-709b96f2ac3e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-uptime-monitor)
[![StyleCI](https://styleci.io/repos/67774357/shield?branch=master)](https://styleci.io/repos/67774357)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-uptime-monitor.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-uptime-monitor)

Laravel-uptime-monitor is a powerful, easy to configure uptime monitor. It can notify you when your site is down (and when it goes back up). You can also be notified a few days before an SSL certificate of one of your sites will expire. Under the hood the package leverages Laravel 5.3's notifications, so it's easy to use Slack, Telegram or any notification provider that has your preference.

## Documentation
You'll find the documentation on [https://docs.spatie.be/laravel-uptime-monitor/v1](https://docs.spatie.be/laravel-uptime-monitor/v1). It includes detailed info on how to install and use the package.

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving the media library? Feel free to [create an issue on GitHub](https://github.com/spatie/laravel-uptime-monitor/issues), we'll try to address it as soon as possible.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

To run the tests you'll have to start the included node based server first

``` bash
cd tests/server
./start_server.sh

cd ../..
vendor/bin/phpunit
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
