# CONTRIBUTING

Contributions are welcome, and are accepted via pull requests.
Please review these guidelines before submitting any pull requests.

## Process

1. Fork the project
1. Create a new branch
1. Code, test, commit and push
1. Open a pull request detailing your changes. Make sure to follow the [template](.github/PULL_REQUEST_TEMPLATE.md)

## Guidelines

- PHP 8.3+.
- Please ensure the coding style running `composer lint`.
- Send a coherent commit history, making sure each individual commit in your pull request is meaningful.
- You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
- Please remember that we follow [SemVer](http://semver.org/).

## Locale and profanity lists

Squeaky is powered by [Pest Profanity Plugin](https://github.com/pestphp/pest-plugin-profanity). Depending on the type of change, extra steps apply:

### Existing locale changes

These changes should be done in Pest Profanity Plugin and a new release should be tagged. Dependabot will then open a PR on this repo. Once that's been merged, it should be good to go because the config will already be getting loaded.

### New locale support

The new locale config will need adding to Pest Profanity Plugin first and a new release should be tagged. Dependabot will then open a PR on this repo. Additionally, the new config will need loading in within the `boot` method of the service provider of this package.

A new case will also need adding to the `JonPurvis\Squeaky\Enums\Locale` enum to support the new locale.

### Functionality changes

For changes to how this rule works, these should be done in this package. No change needed to Pest Profanity Plugin.

## Setup

Clone your fork, then install the dev dependencies:

```bash
composer install
```

## Lint

Lint your code:

```bash
composer lint
```

## Tests

Run all tests:

```bash
composer test
```

Check types:

```bash
composer test:types
```

Type coverage:

```bash
composer test:type-coverage
```

Unit tests:

```bash
composer test:unit
```
