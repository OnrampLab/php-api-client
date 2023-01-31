# php-api-client

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![CircleCI](https://circleci.com/gh/OnrampLab/php-api-client.svg?style=shield)](https://circleci.com/gh/OnrampLab/php-api-client)
[![Total Downloads](https://img.shields.io/packagist/dt/onramplab/php-api-client.svg?style=flat-square)](https://packagist.org/packages/onramplab/php-api-client)

A basic PHP API Client.

## Install

```bash
composer require onramplab/php-api-client
```

## Usage

## Exception Architecture

```
ApiClientException
  ├── ServiceException
  └── HttpException
```

- A `ServiceException` is thrown for 400 level and 500 level errors from third-party service.

- A `HttpException` is thrown when a networking error occurs or too many redirects are followed.


## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email kos.huang@onramplab.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.# php-api-client
