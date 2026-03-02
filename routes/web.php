<?php

use Illuminate\Support\Facades\Route;
use Iquesters\HelpSupport\Http\Controllers\UiController;

Route::get('/help-support/{viewName}', [UiController::class, 'show'])
    ->where('viewName', '[A-Za-z0-9._/-]+')
    ->name('helpsupport.ui.show');
