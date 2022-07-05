<?php

use GaletteHelloAsso\Controllers\NotificationController;
use GaletteHelloAsso\Controllers\CheckoutController;
use GaletteHelloAsso\Controllers\SettingsController;

$this->post('/notify', [NotificationController::class, 'notify'])->setName('helloasso_notify');

$this->get('/checkout', [CheckoutController::class, 'checkout'])->setName('helloasso_checkout');
$this->get('/payment/return', [CheckoutController::class, 'payment_return'])->setName('helloasso_payment_return');
$this->get('/payment/error', [CheckoutController::class, 'payment_error'])->setName('helloasso_payment_error');

$this->get('/settings', [SettingsController::class, 'settings'])->setName('helloasso_settings');
$this->post('/settings', [SettingsController::class, 'save'])->setName('helloasso_settings_save');
