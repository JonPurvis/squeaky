<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;
use JonPurvis\Squeaky\Enums\Locale;
use JonPurvis\Squeaky\Rules\Clean;

beforeEach(function () {
    $this->translator = new Translator(
        new ArrayLoader, 'en'
    );
});

it('fails', function ($word) {
    $v = new Validator($this->translator, ['name' => $word], ['name' => new Clean]);

    expect($v->fails())->toBeTrue()
        ->and($v->errors()->all())->toBe(['The name field is not clean']);
})->with([
    'fuck',
    'shit',
    'bastard',
]);

it('passes', function ($word) {
    $v = new Validator($this->translator, ['name' => $word], ['name' => new Clean]);

    expect($v->passes())->toBeTrue();
})->with([
    'hello',
    'goodbye',
    'greetings',
    'analytics',
]);

test('different language', function ($word) {
    $v = new Validator($this->translator, ['name' => $word], ['name' => new Clean(locales: ['it', 'en', 'da'])]);

    expect($v->fails())->toBeTrue();
})->with([
    'dio accecato',
    'dio affogato nella merda',
    'dio aguzzino',
    'dio ammuffito',
    'fuck',
    'shit',
    'bastard',
    'fandens'
]);

it('passes when using enums', function (Locale $locale) {
    $v = new Validator($this->translator, ['name' => 'hello'], ['name' => new Clean([$locale])]);

    expect($v->passes())->toBeTrue();
})->with(Locale::cases());

it('fails when using enums', function () {
    $v = new Validator($this->translator, ['name' => 'fuck'], ['name' => new Clean([Locale::English])]);

    expect($v->fails())->toBeTrue()
        ->and($v->errors()->all())->toBe(['The name field is not clean']);
});

it('throws an exception if one of the locales is not a string or enum', function () {
    (new Validator(
        $this->translator,
        ['name' => 'hello'],
        ['name' => new Clean([new stdClass()])])
    )->passes();
})->throws(
    exception: InvalidArgumentException::class,
    exceptionMessage: 'The locale must be a string or JonPurvis\Squeaky\Enums\Locale enum.',
);

it('throws an exception if one of the locales does not have a profanity config list', function () {
    (new Validator(
        $this->translator,
        ['name' => 'hello'],
        ['name' => new Clean(['en', 'invalid'])])
    )->passes();
})->throws(
    exception: InvalidArgumentException::class,
    exceptionMessage: 'The locale [\'invalid\'] is not supported.'
);

it('respects custom blocked words from config', function () {
    Config::set('squeaky.blocked_words', ['company_secret', 'internal_term']);
    
    $v = new Validator($this->translator, ['name' => 'company_secret'], ['name' => new Clean]);
    
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->all())->toBe(['The name field is not clean']);
});

it('respects custom allowed words from config', function () {
    Config::set('squeaky.allowed_words', ['analytics', 'scunthorpe']);
    
    $v = new Validator($this->translator, ['name' => 'analytics'], ['name' => new Clean]);
    
    expect($v->passes())->toBeTrue();
});

it('custom blocked words override locale profanity', function () {
    Config::set('squeaky.blocked_words', ['hello']);
    
    $v = new Validator($this->translator, ['name' => 'hello'], ['name' => new Clean]);
    
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->all())->toBe(['The name field is not clean']);
});

it('custom allowed words override locale profanity', function () {
    Config::set('squeaky.allowed_words', ['fuck']);
    
    $v = new Validator($this->translator, ['name' => 'fuck'], ['name' => new Clean]);
    
    expect($v->passes())->toBeTrue();
});

it('respects case sensitivity setting', function () {
    Config::set('squeaky.case_sensitive', true);
    Config::set('squeaky.blocked_words', ['CompanySecret']);
    
    $v = new Validator($this->translator, ['name' => 'companysecret'], ['name' => new Clean]);
    
    expect($v->passes())->toBeTrue();
    
    $v2 = new Validator($this->translator, ['name' => 'CompanySecret'], ['name' => new Clean]);
    
    expect($v2->fails())->toBeTrue()
        ->and($v2->errors()->all())->toBe(['The name field is not clean']);
});

it('case insensitive by default', function () {
    Config::set('squeaky.blocked_words', ['CompanySecret']);
    
    $v = new Validator($this->translator, ['name' => 'companysecret'], ['name' => new Clean]);
    
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->all())->toBe(['The name field is not clean']);
});
