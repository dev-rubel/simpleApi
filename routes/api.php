<?php

Route::apiResource('/person', 'PersonController');

Route::fallback(function () {
   return response()->json([
      'message' => 'Page Not Found'
   ], 404);
});
