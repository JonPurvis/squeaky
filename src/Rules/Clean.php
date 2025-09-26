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

        // Get custom configuration
        $customBlockedWords = Config::get('squeaky.blocked_words', []);
        $customAllowedWords = Config::get('squeaky.allowed_words', []);
        $caseSensitive = Config::get('squeaky.case_sensitive', false);

        // Prepare the value for comparison
        $valueToCheck = $caseSensitive ? $value : Str::lower($value);

        // Check custom blocked words first (these override everything)
        foreach ($customBlockedWords as $blockedWord) {
            $wordToCheck = $caseSensitive ? $blockedWord : Str::lower($blockedWord);
            $regexFlags = $caseSensitive ? '' : 'i';
            
            if (preg_match('/\b' . preg_quote($wordToCheck, '/') . '\b/' . $regexFlags, $valueToCheck)) {
                $fail(trans('message'))->translate([
                    'attribute' => $attribute,
                ], $this->getLocaleValue($locales[0]));

                return;
            }
        }

        // Check locale-specific profanity lists
        foreach ($locales as $locale) {
            $profanities = Config::get($this->configFileName($locale));

            foreach ($profanities as $profanity) {
                // Skip if this word is explicitly allowed
                $profanityToCheck = $caseSensitive ? $profanity : Str::lower($profanity);
                
                if (in_array($profanityToCheck, array_map(function ($word) use ($caseSensitive) {
                    return $caseSensitive ? $word : Str::lower($word);
                }, $customAllowedWords))) {
                    continue;
                }

                $regexFlags = $caseSensitive ? '' : 'i';
                if (preg_match('/\b' . preg_quote($profanityToCheck, '/') . '\b/' . $regexFlags, $valueToCheck)) {
                    $fail(trans('message'))->translate([
                        'attribute' => $attribute,
                    ], $this->getLocaleValue($locale));
                    
                    return;
                }
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
     * Get the locale value expressed as a string.
     */
    protected function getLocaleValue(string|Locale $locale): string
    {
        return $locale instanceof Locale
            ? $locale->value
            : $locale;
    }

    /**
     * Get the name of the config file for the given locale. If a Locale enum is
     * provided, then use the underlying value.
     */
    protected function configFileName(string|Locale $locale): string
    {
        return 'profanify-' . $this->getLocaleValue($locale);
    }
}
