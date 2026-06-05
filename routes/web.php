<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Public Routes
Route::redirect('/', '/login');

// Logout Route (must be authenticated)
Route::post('/logout', function (Request $request, Logout $logout) {
    $logout();
    return redirect('/');
})->middleware('auth')->name('logout');

// Authenticated Routes
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', App\Livewire\Pages\Dashboard::class)->name('dashboard');

    // Profile
    Route::view('profile', 'profile')->name('profile');

    // Master Data Routes
    Route::prefix('master-data')->name('master-data.')->group(function () {
        // Companies
        Route::get('/companies', function () {
            return view('master-data.companies');
        })->middleware('can:companies_view')->name('companies');

        // Modules
        Route::get('/modules', function () {
            return view('master-data.modules');
        })->middleware('can:modules_view')->name('modules');

        // Competencies
        Route::get('/competencies', function () {
            return view('master-data.competencies');
        })->middleware('can:competencies_view')->name('competencies');

        // Personels
        Route::prefix('personels')->name('personels.')->group(function () {
            Route::get('/', function () {
                return view('master-data.personels');
            })->middleware('can:personels_view')->name('index');

            Route::get('/create', function () {
                return view('master-data.personels-create');
            })->middleware('can:personels_create')->name('create');

            Route::get('/{personel}/edit', function (\App\Models\Personel $personel) {
                return view('master-data.personels-edit', ['personel' => $personel]);
            })->middleware('can:personels_update')->name('edit');
        });
    });

    // Projects
    Route::get('/projects', function () {
        return view('projects.index');
    })->middleware('can:projects_view')->name('projects.index');

    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->middleware('can:notifications_view')->name('notifications.index');

    Route::get('/notifications/send', function () {
        return view('notifications.send');
    })->middleware('can:notifications_send')->name('notifications.send');

    // Chat
    Route::get('/chat', function () {
        return view('chat.index');
    })->middleware('can:chat_view')->name('chat.index');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        // System Configuration
        Route::get('/system', function () {
            return view('settings.system');
        })->middleware('can:configuration_view')->name('system');

        // Users Management
        Route::get('/users', function () {
            return view('settings.users');
        })->middleware('can:users_view')->name('users');

        // Roles Management
        Route::get('/roles', function () {
            return view('settings.roles');
        })->middleware('can:roles_view')->name('roles');
    });
});

require __DIR__.'/auth.php';
