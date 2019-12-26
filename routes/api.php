<?php

Route::apiResource('/person', 'PersonController');
Route::get('/multi', 'PersonController@multi');

Route::fallback(function () {
   return response()->json([
      'message' => 'Page Not Found'
   ], 404);
});
