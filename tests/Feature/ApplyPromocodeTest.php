<?php

use AGhorab\LaravelPromocode\Handlers\PercentageDiscountHandler;
use AGhorab\LaravelPromocode\Models\Promocode;
use AGhorab\LaravelPromocode\Tests\MockModels\Cart as MockModelsCart;
use AGhorab\LaravelPromocode\Tests\MockModels\User;
use AGhorab\LaravelPromocode\Tests\Requests\Cart;
use AGhorab\LaravelPromocode\Tests\Requests\CartWithSingleAmountField;

it('test user can apply on promocode', function () {
    /** @var User */
    $user = User::factory()->createOne();
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(3)->createOne();

    $user->applyPromocode($promocode->code);

    expect($promocode->redemptions()->count())->toEqual(1);
});

it('test user can apply on promocode and gain discount', function () {
    /** @var User */
    $user = User::factory()->createOne();
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(3)->discount(new PercentageDiscountHandler(20))->createOne();

    $cart = new Cart(1000);

    $user->applyPromocode($promocode->code, $cart);

    expect($cart->amount)->toEqual(800);
    expect($cart->discount_amount)->toEqual(200);
    expect($cart->original_amount)->toEqual(1000);
});

it('test user can apply on promocode and gain discount without discount amount', function () {
    /** @var User */
    $user = User::factory()->createOne();
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(3)->discount(new PercentageDiscountHandler(20))->createOne();

    $cart = new CartWithSingleAmountField(1000);

    $user->applyPromocode($promocode->code, $cart);

    expect($cart->amount)->toEqual(800);
});

it('test user can apply on promocode and gain discount and connect to model', function () {
    /** @var User */
    $user = User::factory()->createOne();
    /** @var Promocode */
    $promocode = Promocode::factory()->singleUse()->totalUsage(3)->discount(new PercentageDiscountHandler(20))->createOne();

    $cart = MockModelsCart::factory()->amount(1000)->create();

    $user->applyPromocode($promocode->code, $cart);

    expect($cart->amount)->toEqual(800);
    expect($cart->discount_amount)->toEqual(200);
    expect($cart->original_amount)->toEqual(1000);
});
