<?php

use GaletteHelloAsso\Controllers\NotificationController;

$this->post('/notify', [NotificationController::class, 'notify'])->setName('helloasso_notify');
