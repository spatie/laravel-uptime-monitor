# Changelog

All notable changes to `laravel-uptime-monitor` will be documented in this file

## Unreleased

## 3.7.0 - 2020-02-14

- allow configuration of guzzle client options (#181)

## 3.5.0 - 2019-05-17

- Add `raw_url` attribute to serialization [#175](https://github.com/spatie/laravel-uptime-monitor/pull/175)

## 3.4.1 - 2019-04-15

- Fixed issue with migrations stub ([#171](https://github.com/spatie/laravel-uptime-monitor/pull/171))

## 3.4.0 - 2019-03-03

- Dropped support for Laravel 5.7
- Added support for Laravel 5.8, PHPUnit 8
- PHPUnit minimum version is now 7.5

## 3.3.4 - 2018-10-30

- fix if statement to be if not certificate applies to url

## 3.3.3 - 2018-10-20

- fix for PHP 7.3

## 3.3.2 - 2018-10-18

- fix for checking SSL

## 3.3.1 - 2018-08-27

- add support for Laravel 5.7

## 3.3.0 - 2018-03-13

- add option to force run all monitors

## 3.2.1 - 2018-02-08

- add support for L5.6

## 3.2.0 - 2017-12-20

- add ability for monitors to have their own response checkers

## 3.1.0 - 2017-12-11

- add ability to send payload to verify uptime

## 3.0.0 - 2017-08-31

- add support for Laravel 5.5, drop support for Laravel 5.4
- renamed config file from `laravel-uptime-monitor` to `uptime-monitor`

## 2.2.0 - 2017-03-13

- add `retry_connection_after_milliseconds` to config file

## 2.1.0 - 2017-03-13

- add `sync` command

## 2.0.3 - 2017-03-13

- fixed bug in getting unchecked monitors

## 2.0.2 - 2017-03-08

- added monitor location to mail notifications

## 2.0.1 - 2017-01-27

- ask for protocol when creating a monitor

## 2.0.0 - 2017-01-24

- add support for L5.4
- drop support for L5.3

## 1.2.3 - 2017-01-14

- fixed bug where migration could be published multiple times

## 1.2.2 - 2017-01-06

- set fallback text for Slack notifications

## 1.2.1 - 2016-12-22

- fix typos in notifications

## 1.2.0 - 2016-12-22

- improve notifications

## 1.1.2 - 2016-12-19

- fix `CertificateCheckSucceeded` notification

## 1.1.1 - 2016-12-12

- fix typos in command descriptions

## 1.1.0 - 2016-12-03

- added `additional_headers` to config

## 1.0.2 - 2016-11-24

- fix descriptions in config file

## 1.0.1 - 2016-11-21

- fix custom model instructions in config file

## 1.0.0 - 2016-11-21

- initial release
