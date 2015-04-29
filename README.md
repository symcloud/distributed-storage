# Distributed-Storage

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/symcloud/distributed-storage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/symcloud/distributed-storage/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/symcloud/distributed-storage/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/symcloud/distributed-storage/?branch=master)
[![Build Status](https://travis-ci.org/symcloud/distributed-storage.svg?branch=master)](https://travis-ci.org/symcloud/distributed-storage)

Storage System of symcloud application

## Development

This library is currently under heavy development and the interfaces can change without warning.

## Documentation

See [docs/README.md](https://github.com/symcloud/distributed-storage/blob/master/doc/README.md)

## Requirements

* Symfony: >= 2.6.*
* See also the require section of [composer.json](https://github.com/symcloud/distributed-storage/blob/master$

## Contribute

This project was initiated for a master thesis. Anyway pull-requests are welcome. (-:

### Dependencies

* [composer](https://getcomposer.org/)
* [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

### Run tests

```bash
composer update
phpunit
```

### Fix code style

```bash
php-cs-fixer fix src
```
