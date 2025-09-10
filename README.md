<img src="art/banner.png">

# Squeaky
A Laravel Validation Rule that helps catch profanity in your application.

[![Tests](https://github.com/JonPurvis/squeaky/actions/workflows/tests.yml/badge.svg)](https://github.com/JonPurvis/squeaky/actions/workflows/tests.yml)
![GitHub last commit](https://img.shields.io/github/last-commit/jonpurvis/squeaky)
![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/jonpurvis/squeaky/php)
![GitHub issues](https://img.shields.io/github/issues/jonpurvis/squeaky)
![GitHub](https://img.shields.io/github/license/jonpurvis/squeaky)
![Packagist Downloads](https://img.shields.io/packagist/dt/jonpurvis/squeaky)

## Introduction
Squeaky (short for Squeaky Clean) is a Laravel validation rule that you can add your Laravel application, to ensure any
user submitted input such as a name or biography, is free of profanity and therefore, clean. Just add `new Clean()` to
your rules and you're good to go!

Squeaky is powered by [Pest Profanity Plugin](https://github.com/pestphp/pest-plugin-profanity), which is a PestPHP Plugin that
does the same thing, but for code. By utilising the profanity in that package, Squeaky is powerful from the get-go and
provides support for numerous locales, not just English.

## Installation

To install Squeaky, you can run the following command in your project's root:

```text
composer require jonpurvis/squeaky
```

After installation, you can publish the configuration file to customize the package behavior:

```bash
php artisan vendor:publish --provider="JonPurvis\Squeaky\SqueakyServiceProvider"
```

This will create a `config/squeaky.php` file in your application where you can configure custom blocked and allowed words.

## Examples

Let's take a look at how Squeaky works. As it's a Laravel Validation rule, there's not really that much to it. You 
would use it in the same way you would use a custom validation rule you've added yourself.

Let's take the following scenario where we have a form that allows a user to enter their name, email and bio:

```php
use App\Models\User;
use Illuminate\Validation\Rule;
use JonPurvis\Squeaky\Rules\Clean;

return [
    'name' => ['required', 'string', 'max:255', new Clean],
    'email' => [
        'required',
        'string',
        'lowercase',
        'email',
        'max:255',
        Rule::unique(User::class)->ignore($this->user()->id),
    ],
    'bio' => ['required', 'string', 'max:255', new Clean],
];
```

You'll notice that both _name_ and _bio_ are using the **Clean** rule. This rule will take the value and ensure that 
it doesn't exist in the profanity config files that the package has. By default, it will use your app locale, so if 
your locale is set to `en`, it will scan profanity in the _en_ profanity config.

If profanity is found, an error will appear in the validation errors array and will be shown to your user 
(if your application does this).

Some applications allow for more than one language, so you're able to pass in additional locales to the rule to cater 
for them. Below is an example showing how to cater for both `en` and `it`:

```php
use JonPurvis\Squeaky\Rules\Clean;

'name' => ['required', 'string', 'max:255', new Clean(['en', 'it'])],
```

Alternatively, instead of passing an array of strings to the rule, you can pass in an array of `JonPurvis\Squeaky\Enums\Locale` enums to specify the locales:

```php
use JonPurvis\Squeaky\Enums\Locale;
use JonPurvis\Squeaky\Rules\Clean;

'name' => ['required', 'string', 'max:255', new Clean([Locale::English, Locale::Italian])],
```


This will then check the locale config for any locale you've passed in (providing the config exists!). If profanity is 
found in any of them, an error will appear in the validation errors. 

The really cool thing about this, is the error will be returned in the language that failed. So if the failure was
found in the `it` profanity list, the package assumes the user is Italian and returns the error message in Italian to
let them know that their value is not clean.

## Configuration

Squeaky provides a publishable configuration file that allows you to customize the package behavior for your specific application needs.

### Custom Words

You can specify custom words that should be treated as profanity or explicitly allowed:

```php
// config/squeaky.php
return [
    'blocked_words' => [
        'company_secret',
        'internal_term',
        'restricted_word',
    ],
    
    'allowed_words' => [
        'analytics',
        'scunthorpe',
        'penistone',
    ],
    
    'case_sensitive' => false,
];
```

- **`blocked_words`**: Words that will be treated as profanity regardless of the locale-specific profanity lists. These override everything else.
- **`allowed_words`**: Words that will be explicitly allowed even if they appear in the locale-specific profanity lists.
- **`case_sensitive`**: Determines whether word matching should be case-sensitive. Defaults to `false` for case-insensitive matching.

### Priority Order

The validation follows this priority order:
1. Custom blocked words (highest priority - always blocked)
2. Custom allowed words (override locale profanity)
3. Locale-specific profanity lists (lowest priority) 

## Languages
Squeaky currently supports the following languages:

- Arabic
- Danish
- Danish
- English
- German
- Spanish
- Italian
- Japanese
- Dutch
- Brazilian Portuguese
- Russian

## Contributing
Contributions to the package are more than welcome! Depending on the type of change, there's a few extra steps that will
need carrying out:

### Existing Locale Changes
These changes should be done in [Pest Profanity Plugin](https://github.com/pestphp/pest-plugin-profanit) and a new 
release should be tagged. Dependabot will then open a PR on this repo. Once that's been merged, it should be good to 
go because the config will already be getting loaded.

### New Locale Support
The new locale config will need adding to [Pest Profanity Plugin](https://github.com/pestphp/pest-plugin-profanit)
first and a new release should be tagged. Dependabot will then open a PR on this repo. Additionally, the new config 
will need loading in within the `boot` method of the service provider of this package. 

A new case will also need adding to the `JonPurvis/Squeaky/Enums/Locale` enum to support the new locale.

### Functionality Changes
For changes to how this rule works, these should be done in this package. No change needed to Pest Profanity Plugin.
