# SilverStripe Foxy.io Orders

Add order history to your SilverStripe Foxy store.

[![Latest Stable Version](https://poser.pugx.org/dynamic/silverstripe-foxy-orders/v/stable)](https://packagist.org/packages/dynamic/silverstripe-foxy-orders)
[![Total Downloads](https://poser.pugx.org/dynamic/silverstripe-foxy-orders/downloads)](https://packagist.org/packages/dynamic/silverstripe-foxy-orders)
[![License](https://poser.pugx.org/dynamic/silverstripe-foxy-orders/license)](https://packagist.org/packages/dynamic/silverstripe-foxy-orders)

## Requirements

* PHP ^8.1
* SilverStripe ^5.0
* SilverStripe Foxy Feed Parser ^2.0

## Installation

```bash
composer require dynamic/silverstripe-foxy-orders
```

## License

See [License](LICENSE.md)

## Configuration

Add the following to your project's YAML configuration:

```yaml
SilverStripe\Security\Member:
  extensions:
    - Dynamic\Foxy\Orders\Extension\MemberDataExtension
```

## Maintainers

* [Dynamic](http://www.dynamicagency.com) (<dev@dynamicagency.com>)

## Bugtracker

Bugs are tracked in the issues section of this repository. Before submitting an issue please read over existing issues to ensure yours is unique.

If the issue does look like a new bug:

- Create a new issue
- Describe the steps required to reproduce your issue
- Describe your environment: SilverStripe version, PHP version, Operating System

Please report security issues to the module maintainers directly.

## Development and Contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
