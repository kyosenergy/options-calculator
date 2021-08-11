# Black-Scholes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kyosenergy/black-scholes.svg?style=flat-square)](https://packagist.org/packages/kyosenergy/black-scholes)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kyosenergy/black-scholes/run-tests?label=tests)](https://github.com/kyosenergy/black-scholes/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/kyosenergy/black-scholes/Check%20&%20fix%20styling?label=code%20style)](https://github.com/kyosenergy/black-scholes/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/kyosenergy/black-scholes.svg?style=flat-square)](https://packagist.org/packages/kyosenergy/black-scholes)

An option price/volatility calculator using the Black-Scholes formula.

## Installation

You can install the package via composer:

```bash
composer require kyos/black-scholes
```

## Usage

```php
$bs = new Kyos\BlackScholes(60, 65, 0.25, 8, 30);
echo $bs->valueCall(); // 60.0
echo $bs->valuePut(); // 8.796793410
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Emilian Enev](https://github.com/Enev)
- [Zois Pagoulatos](https://github.com/zoispag)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
