<?php

use AGhorab\LaravelPromocode\Models\Promocode;
use AGhorab\LaravelPromocode\Models\PromocodeRedemption;
use AGhorab\LaravelPromocode\Rules\ValidPromocode;
use AGhorab\LaravelPromocode\Tests\MockModels\User;
use Illuminate\Validation\ValidationException;

it('validate promocode as string', function () {
    validator([
        'code' => 123,
    ], [
        'code' => [new ValidPromocode()],
    ])->validate();

})->throws(ValidationException::class);

it('Promocode Bounded to another user', function () {
    /** @var User */
    [$user, $otherUser] = User::factory()->count(2)->create();

    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->boundedReedemer($user)->createOne();

    validator([
        'code' => $promocode->code,
    ], [
        'code' => [new ValidPromocode($otherUser)],
    ])->validate();

})->throws(ValidationException::class);

it('Promocode Usage Exceeded', function () {
    /** @var Promocode */
    $promocode = Promocode::factory()->has(PromocodeRedemption::factory()->forUser(User::factory()->createOne())->count(1), 'redemptions')->singleUse()->totalUsage(1)->createOne();

    /** @var User */
    $user = User::factory()->createOne();

    validator([
        'code' => $promocode->code,
    ], [
        'code' => [new ValidPromocode($user)],
    ])->validate();

})->throws(ValidationException::class);

it('Promocode Already Applied', function () {
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(2)->createOne();

    /** @var User */
    $user = User::factory()->createOne();

    $user->applyPromocode($promocode->code);

    validator([
        'code' => $promocode->code,
    ], [
        'code' => [new ValidPromocode($user)],
    ])->validate();

})->throws(ValidationException::class);

it('Promocode Already Expired', function () {
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(2)->expired()->createOne();

    /** @var User */
    $user = User::factory()->createOne();

    validator([
        'code' => $promocode->code,
    ], [
        'code' => [new ValidPromocode($user)],
    ])->validate();

})->throws(ValidationException::class);
