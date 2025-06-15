<?php

namespace JonPurvis\Squeaky;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SqueakyServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        $this->bootPackageTranslations();

        // Set Profanify path
        if (getenv('RUNNING_TESTS') === '1') {
            $profanifyBasePath = realpath('vendor/pestphp/pest-plugin-profanity/src/Config');
        } else {
            $profanifyBasePath = base_path('vendor').'/pestphp/pest-plugin-profanity/src/Config';
        }

        // Language Specific Configs
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/ar.php', 'profanify-ar');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/da.php', 'profanify-da');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/en.php', 'profanify-en');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/it.php', 'profanify-it');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/ja.php', 'profanify-ja');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/nl.php', 'profanify-nl');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/pt_BR.php', 'profanify-pt_BR');
        $this->mergeConfigFrom($profanifyBasePath.'/profanities/es.php', 'profanify-es');
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('squeaky')
            ->hasTranslations();
    }
}
