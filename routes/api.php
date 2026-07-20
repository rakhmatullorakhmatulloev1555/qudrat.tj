<?php

use App\Http\Controllers\Api\Apartment3DController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API — «3D Квартиры» (публичное чтение)
|--------------------------------------------------------------------------
| Префикс /api добавляется автоматически. throttle защищает от абьюза.
*/
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/apartments/{apartment}/3d',        [Apartment3DController::class, 'scene'])->name('api.apartments.3d');
    Route::get('/apartments/{apartment}/styles',    [Apartment3DController::class, 'styles'])->name('api.apartments.styles');
    Route::get('/apartments/{apartment}/rooms',     [Apartment3DController::class, 'rooms'])->name('api.apartments.rooms');
    Route::get('/apartments/{apartment}/hotspots',  [Apartment3DController::class, 'hotspots'])->name('api.apartments.hotspots');
    Route::get('/apartments/{apartment}/tour',      [Apartment3DController::class, 'tour'])->name('api.apartments.tour');
});
