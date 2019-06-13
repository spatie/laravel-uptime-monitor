---
title: Events
weight: 2
---

These events are fired by a monitor's ssl certificate check.

## CertificateCheckFailed

`Spatie\UptimeMonitor\Events\CertificateCheckFailed`

This event is fired when the certificate check cannot find a certificate or if the certificate is invalid. A certificate is considered invalid if it is expired or it not covering correct domain.

It has the following public properties:

- `$monitor`: the instance of `Spatie\UptimeMonitor\Models\Monitor` that fired the event
- `$reason`: a string explaining why the certificate check failed
- `$certificate`: if a certificate was found, this variable contains an instance of `\Spatie\SslCertificate\SslCertificate`. Refer to the [documentation of `spatie/ssl-certificate`](https://github.com/spatie/ssl-certificate) to learn how to work with this object. 

## CertificateCheckSucceeded

`Spatie\UptimeMonitor\Events\CertificateCheckSucceeded`

This event is fired after the certificate check finds a valid certificate.

It has the following public properties:

- `$monitor`: the instance of `Spatie\UptimeMonitor\Models\Monitor` that fired of the event
- `$certificate`: if a valid certificate is found, this variable contains an instance of `\Spatie\SslCertificate\SslCertificate`. Refer to the [documentation of `spatie/ssl-certificate`](https://github.com/spatie/ssl-certificate) to learn how to work with this object. 

## CertificateExpiresSoon

`Spatie\UptimeMonitor\Events\CertificateExpiresSoon`

This event is fired in addition to `CertificateCheckSucceeded` when the certificate check finds an ssl certificate that is going to expire in the number of days configured in `fire_expiring_soon_event_if_certificate_expires_within_days` or less.

It has these public properties:

- `$monitor`: the instance of `Spatie\UptimeMonitor\Models\Monitor` that fired the event
- `$certificate`: if an expiring certificate is found, this variable contains an instance of `\Spatie\SslCertificate\SslCertificate` Refer to the [documentation of `spatie/ssl-certificate`](https://github.com/spatie/ssl-certificate) to learn how to work with this object. 
