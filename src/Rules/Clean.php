<?php

namespace JonPurvis\Squeaky\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JonPurvis\Squeaky\Enums\Locale;

class Clean implements ValidationRule
{
    public function __construct(
        public ?array $locales = [],
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $locales = empty($this->locales) ? [Config::get('app.locale')] : $this->locales;

        $this->ensureLocalesAreValid($locales);

        foreach ($locales as $locale) {
            $profanities = Config::get($this->configFileName($locale));
            $tolerated = Config::get('profanify-tolerated');

            if (Str::contains(Str::lower(Str::remove($tolerated, $value)), $profanities)) {
                $fail(trans('message'))->translate([
                    'attribute' => $attribute,
                ], $locale instanceof Locale ? $locale->value : $locale);
            }
        }
    }

    /**
     * Check that a config file exists for each locale. If one of the locales
     * is invalid, then throw an exception.
     */
    protected function ensureLocalesAreValid(array $locales): void
    {
        foreach ($locales as $locale) {
            if (!is_string($locale) && !$locale instanceof Locale) {
                throw new InvalidArgumentException('The locale must be a string or JonPurvis\Squeaky\Enums\Locale enum.');
            }

            if (!Config::has($this->configFileName($locale))) {
                throw new InvalidArgumentException("The locale ['{$locale}'] is not supported.");
            }
        }
    }

    /**
     * Get the name of the config file for the given locale. If a Locale enum is
     * provided, then use the underlying value.
     */
    protected function configFileName(string|Locale $locale): string
    {
        $locale = $locale instanceof Locale
            ? $locale->value
            : $locale;

        return 'profanify-' . $locale;
    }
}
