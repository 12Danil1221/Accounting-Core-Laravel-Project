<?php

use App\Http\Controllers\AdvantagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdvantagesController::class, 'index']);