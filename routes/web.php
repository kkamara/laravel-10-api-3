<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("/setup", function() {
    $credentials = [
        "email" => "admin@admin.com",
        "password" => "password"
    ];

    if (!Auth::attempt($credentials)) {
        $user = new User();

        $user->first_name = "Admin";
        $user->last_name = "";
        $user->email = $credentials["email"];
        $user->password = Hash::make($credentials["password"]);

        $user->save();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $adminToken = $user->createToken(
                "admin-token",
                ["create", "update", "delete"]
            );
            $updateToken = $user->createToken(
                "update-token",
                ["create", "update"]
            );
            $basicToken = $user->createToken(
                "basic-token"
            );

            return [
                "admin" => $adminToken
                    ->plainTextToken,
                "update" => $updateToken
                    ->plainTextToken,
                "basic" => $basicToken
                    ->plainTextToken,
            ];
        }
    }
});

require __DIR__.'/auth.php';
