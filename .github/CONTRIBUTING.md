# Contributing to php-cypher-dsl

Welcome! We look forward to your contributions. This document outlines the guidelines for contributing to php-cypher-dsl. Keep in mind that these guidelines are mostly just suggestions, and shouldn't hold you back from improving php-cypher-dsl. Don't be afraid to ignore them.

Below are some examples on how you can contribute:

* [Report a bug](https://github.com/neo4j-php/php-cypher-dsl/issues/new?template=bug_report.md)
* [Propose a new feature](https://github.com/neo4j-php/php-cypher-dsl/issues/new?template=missing_feature_request.md)
* [Send a pull request](https://github.com/neo4j-php/php-cypher-dsl/pulls)

## Code of conduct

This project has a [Contributor Code of Conduct](https://github.com/neo4j-php/php-cypher-dsl/blob/main/CODE_OF_CONDUCT.md). By contributing to or participating in this project, you agree to abide to its terms.

## Any contributions will be licensed under the GPL 2.0 or any later version

When submitting code or changes, your submissions will automatically be licensed under the [GNU General Public License](https://github.com/neo4j-php/php-cypher-dsl/blob/main/LICENSE) version 2.0 or later. By contributing to this project, you agree that your contributions will be licensed under its terms.

## Writing bug reports

In your bug report, you should provide the following:

* A short summary of the bug
* What you expect would happen
* What actually happens
* Steps to reproduce
  * Be specific! Give sample code if possible.
  * Include the version of PHP and php-cypher-dsl.

You should only report bugs for versions of php-cypher-dsl that [are supported](https://github.com/neo4j-php/php-cypher-dsl/blob/main/LIFECYCLE.md). Please only report bugs if you are using a php-cypher-dsl with a [compatible version of PHP](https://github.com/neo4j-php/php-cypher-dsl/blob/main/LIFECYCLE.md).

## Proposing new features

Feel free to propose a new feature by [opening an issue for it](https://github.com/neo4j-php/php-cypher-dsl/issues/new?template=missing_feature_request.md).

## Workflow for pull requests

1. Fork the repository.
1. Create a new branch.
   1. If you are **implementing new functionality**, create your branch from `main`.
   1. If you are **fixing a bug**, create your branch from the oldest [supported](https://github.com/neo4j-php/php-cypher-dsl/blob/main/LIFECYCLE.md) branch that is affected by the bug.
1. Implement your change and add tests for it.
1. Make sure the test suite passes.
1. Make sure that the code complies with the coding guidelines (see below).
1. Submit your pull request!

Some things to keep in mind:

* Make sure that you have [configured a user name and email address](https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup) for use with Git.
* Keep backwards compatibility breaks to a minimum.
* You are encouraged to [sign your commits](https://docs.github.com/en/authentication/managing-commit-signature-verification/signing-commits) with GPG.

## Coding guidelines

This project comes with a [configuration file](https://github.com/neo4j-php/php-cypher-dsl/blob/main/.php-cs-fixer.dist.php) for `php-cs-fixer` that you can use to format your code:

```
$ php vendor/bin/php-cs-fixer fix --config .php-cs-fixer.dist.php
```

This project uses PHPStan for static analysis:

```
$ php vendor/bin/phpstan
```

After making your changes and adding your tests, you can check whether the minimum coverage and minimum mutation score indicator requirements are still met:

```
$ XDEBUG_MODE=coverage php vendor/bin/phpunit --testsuite unit
$ php vendor/bin/coverage-check coverage/clover.xml 90
$ XDEBUG_MODE=coverage php vendor/bin/infection --min-msi=85
```

## Running test suites

To run all tests, use:

```
$ php vendor/bin/phpunit --no-coverage
```

To only run *unit* tests, use:

```
$ php vendor/bin/phpunit --testsuite unit --no-coverage
```

To only run *end-to-end* tests, use:

```
$ php vendor/bin/phpunit --testsuite end-to-end --no-coverage
```
