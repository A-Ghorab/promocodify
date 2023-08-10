<?php

use AGhorab\LaravelPromocode\Models\Promocode;
use AGhorab\LaravelPromocode\Models\PromocodeUsage;
use AGhorab\LaravelPromocode\Tests\MockModels\User;

it('should return available promocodes', function () {
    Promocode::factory()->expired()->count(5)->create();
    Promocode::factory()->notExpired()->count(5)->create();

    expect(Promocode::available()->count())->toEqual(5);
});

it('shouldn\'t have usage left', function () {
    User::factory()->count(5)->create();

    /** @var Promocode */
    $promocode = Promocode::factory()->has(PromocodeUsage::factory()->count(3), 'usages')->totalUsage(3)->create();

    expect($promocode->hasUsagesLeft())->toBeFalse();
});

it('shouldn have usage left', function () {
    User::factory()->count(5)->create();

    /** @var Promocode */
    $promocode = Promocode::factory()->has(PromocodeUsage::factory()->count(3), 'usages')->totalUsage(100)->create();

    expect($promocode->hasUsagesLeft())->tobeTrue();
});

it('select only promocode with avaliable usage', function () {
    User::factory()->count(5)->create();

    Promocode::factory()->unlimited()->count(3)->create();
    Promocode::factory()->totalUsage(5)->count(5)->create();
    Promocode::factory()->has(PromocodeUsage::factory()->count(3), 'usages')->totalUsage(3)->count(3)->create();
    Promocode::factory()->has(PromocodeUsage::factory()->count(3), 'usages')->totalUsage(5)->count(3)->create();

    expect(Promocode::query()->hasUsage()->count())->toEqual(11);
});

it('select only promocode allowed for user', function () {
    User::factory()->count(5)->create();

    Promocode::factory()->unlimited()->count(3)->create();
    Promocode::factory()->totalUsage(5)->count(5)->create();

    // create user and link promocode with it
    $user = User::factory()->createOne();
    Promocode::factory()->has(PromocodeUsage::factory()->forUser($user)->count(1), 'usages')->singleUse()->totalUsage(5)->count(3)->create();

    expect(Promocode::query()->hasUsageForUser($user)->count())->toEqual(8);
});

it('user can use promocode multiple times if multi use is enabled', function () {
    User::factory()->count(5)->create();

    Promocode::factory()->unlimited()->count(3)->create();
    Promocode::factory()->totalUsage(5)->count(5)->create();

    // create user and link promocode with it
    $user = User::factory()->createOne();
    Promocode::factory()->has(PromocodeUsage::factory()->forUser($user)->count(1), 'usages')->multiUse()->totalUsage(5)->count(3)->create();

    expect(Promocode::query()->hasUsageForUser($user)->count())->toEqual(11);
});