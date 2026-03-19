# MyAdmin WHMSonic Licensing

[![Build Status](https://github.com/detain/myadmin-whmsonic-licensing/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-whmsonic-licensing/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-whmsonic-licensing/version)](https://packagist.org/packages/detain/myadmin-whmsonic-licensing)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-whmsonic-licensing/downloads)](https://packagist.org/packages/detain/myadmin-whmsonic-licensing)
[![License](https://poser.pugx.org/detain/myadmin-whmsonic-licensing/license)](https://packagist.org/packages/detain/myadmin-whmsonic-licensing)

A MyAdmin plugin for managing WHMSonic streaming-media licenses. This package integrates with the WHMSonic reseller API to provide automated license activation, suspension, unsuspension, termination, listing, and verification directly from the MyAdmin control panel.

WHMSonic is a WHM/cPanel plugin for hosting shoutcast and icecast streaming-media services. This plugin allows hosting providers to sell and manage WHMSonic license types (yearly, monthly, and lifetime) through the MyAdmin billing and provisioning system.

## Features

- License activation via the WHMSonic reseller API
- License suspension and unsuspension
- License termination
- License listing by type (yearly, monthly, lifetime, or all)
- License verification by server IP
- IP address change support
- Symfony EventDispatcher integration for hook-based architecture
- Admin menu integration for license management

## Requirements

- PHP 8.2 or higher
- cURL extension
- Symfony EventDispatcher 5.x, 6.x, or 7.x

## Installation

Install with Composer:

```sh
composer require detain/myadmin-whmsonic-licensing
```

## Running Tests

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the [LGPL-2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html) license.
