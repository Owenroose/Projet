<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TeamMemberController as AdminTeamMemberController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

// Routes pour les commandes
Route::get('/products/{slug}/order', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Routes pour le panier
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/content', [CartController::class, 'getCartContent'])->name('content');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
    Route::post('/sync', [CartController::class, 'sync'])->name('sync');
});

// Routes de gestion des commandes (Front-end)
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/create/{slug?}', [OrderController::class, 'create'])->name('create');
    Route::post('/store', [OrderController::class, 'store'])->name('store');

    // Routes pour FedaPay - CORRIGÉES
    Route::post('/payment/callback', [OrderController::class, 'handleCallback'])->name('payment.callback');
    Route::get('/payment/cancel', [OrderController::class, 'cancel'])->name('cancel');

    // Route de succès de commande (après le paiement)
    Route::get('/success/{orderGroup}', [OrderController::class, 'showSuccess'])->name('success');

    // Route de facture
    Route::get('/{order}/invoice', [OrderController::class, 'showInvoice'])->name('invoice');

    // Route d'échec de paiement
    Route::get('/failure', function () {
        return view('orders.failure');
    })->name('failure');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Routes pour les projets
    Route::resource('projects', AdminProjectController::class);

    // Routes pour les services
    Route::resource('services', AdminServiceController::class);
    Route::put('services/{service}/toggle-published', [AdminServiceController::class, 'togglePublished'])->name('services.togglePublished');

    // Routes pour les produits
    Route::resource('products', AdminProductController::class);

    // Routes pour les témoignages
    Route::resource('testimonials', AdminTestimonialController::class);

    // Routes pour l'équipe
    Route::resource('team-members', AdminTeamMemberController::class);

    // Routes pour les contacts
    Route::resource('contacts', AdminContactController::class)->except(['create', 'store']);
    Route::post('contacts/{contact}/response', [AdminContactController::class, 'sendResponse'])->name('contacts.send-response');
    Route::post('contacts/{contact}/mark-as-read', [AdminContactController::class, 'markAsRead'])->name('contacts.mark-as-read');
    Route::post('contacts/{contact}/mark-as-unread', [AdminContactController::class, 'markAsUnread'])->name('contacts.mark-as-unread');
    Route::post('contacts/bulk-action', [AdminContactController::class, 'bulkAction'])->name('contacts.bulk-action');

    // Routes pour le profil
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    // Routes pour l'assistant IA
    Route::post('ai-assistant', [AiAssistantController::class, 'handleRequest'])->name('ai-assistant');
    Route::get('ai-stats', [AiAssistantController::class, 'aiStats'])->name('ai-stats');

    // Routes pour les utilisateurs
    Route::controller(AdminUserController::class)->prefix('users')->name('users.')->group(function() {
        // Routes CRUD de base
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{user}', 'show')->name('show');
        Route::get('{user}/edit', 'edit')->name('edit');
        Route::put('{user}', 'update')->name('update');
        Route::delete('{user}', 'destroy')->name('destroy');

        // Routes de gestion des rôles
        Route::get('{user}/roles', 'getUserRoles')->name('get-roles');
        Route::post('{user}/assign-role', 'assignRole')->name('assign-role');
        Route::post('{user}/remove-role', 'removeRole')->name('remove-role');

        // Routes d'informations utilisateur
        Route::get('{user}/quick-info', 'quickInfo')->name('quick-info');
        Route::get('{user}/activity-log', 'activityLog')->name('activity-log');

        // Actions en lot
        Route::post('bulk-actions', 'bulkActions')->name('bulk-actions');
        Route::post('bulk-update', 'bulkUpdate')->name('bulk-update');
        Route::post('bulk-assign-role', 'bulkAssignRole')->name('bulk-assign-role');

        // Import/Export
        Route::post('import', 'import')->name('import');
        Route::get('export', 'exportUsers')->name('export');

        // Actions individuelles
        Route::patch('{user}/reset-password', 'resetPassword')->name('reset-password');
        Route::patch('{user}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::patch('{user}/force-verification', 'forceVerification')->name('force-verification');
        Route::post('{user}/send-verification-email', 'sendVerificationEmail')->name('send-verification-email');

        // Statistiques et rapports
        Route::get('statistics', 'statistics')->name('statistics');
        Route::get('report', 'generateReport')->name('generate-report');

        // Recherche
        Route::get('search', 'search')->name('search');
        Route::get('search-autocomplete', 'searchAutocomplete')->name('search-autocomplete');
    });

    // Routes pour les commandes
    Route::controller(AdminOrderController::class)->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/{order}', 'show')->name('show');
        Route::delete('/{order}', 'destroy')->name('destroy');

        // Actions AJAX
        Route::patch('/{order}/status', 'updateStatus')->name('update-status');
        Route::post('/{order}/note', 'addNote')->name('add-note');
        Route::patch('/{order}/priority', 'togglePriority')->name('toggle-priority');

        // Recherche et rapports
        Route::get('/search/autocomplete', 'searchAutocomplete')->name('search-autocomplete');
        Route::post('/generate-report', 'generateReport')->name('generate-report');
    });

    // Routes pour les rôles
    Route::resource('roles', AdminRoleController::class);
    Route::post('roles/{role}/assign-user', [AdminRoleController::class, 'assignUser'])->name('roles.assign-user');
    Route::delete('roles/{role}/remove-user', [AdminRoleController::class, 'removeUser'])->name('roles.remove-user');
    Route::post('roles/{role}/assign-permission', [AdminRoleController::class, 'assignPermission'])->name('roles.assign-permission');
    Route::delete('roles/{role}/remove-permission', [AdminRoleController::class, 'removePermission'])->name('roles.remove-permission');
});

// Breeze authentication routes
require __DIR__.'/auth.php';
