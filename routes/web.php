<?php

use Illuminate\Support\Facades\Route;
use App\Http\PaymentGateways\Gateways\Paytm;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Frontend\RootController;

use App\Http\Controllers\Frontend\BrandController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Installer\InstallerController;
use App\Http\Controllers\DashboardV2\UserProfileController;
use App\Http\Controllers\Frontend\ChefController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::prefix('install')->name('installer.')->middleware(['web'])->group(function () {
    Route::get('/', [InstallerController::class, 'index'])->name('index');
    Route::get('/requirement', [InstallerController::class, 'requirement'])->name('requirement');
    Route::get('/permission', [InstallerController::class, 'permission'])->name('permission');
    Route::get('/license', [InstallerController::class, 'license'])->name('license');
    Route::post('/license', [InstallerController::class, 'licenseStore'])->name('licenseStore');
    Route::get('/site', [InstallerController::class, 'site'])->name('site');
    Route::post('/site', [InstallerController::class, 'siteStore'])->name('siteStore');
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database', [InstallerController::class, 'databaseStore'])->name('databaseStore');
    Route::get('/final', [InstallerController::class, 'final'])->name('final');
    Route::get('/final-store', [InstallerController::class, 'finalStore'])->name('finalStore');
});


Route::get('/', [RootController::class, 'index'])->middleware(['installed'])->name('home');
Route::prefix('payment')->name('payment.')->middleware(['installed'])->group(function () {
    Route::get('/{order}/pay', [PaymentController::class, 'index'])->name('index');
    Route::post('/{order}/pay', [PaymentController::class, 'payment'])->name('store');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/success', [PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/successful/{order}', [PaymentController::class, 'successful'])->name('successful');
});

// Route::get('dashboardv2/users', [UserProfileController::class, 'index'])
//     ->name('admin.users.index');
Route::get('dashboardv2/order-ratings', [UserProfileController::class, 'orderRatings'])->name('dashboard.order_ratings');

Route::get('/admin/orders/preparation-time', [UserProfileController::class, 'managePreparationTime'])->name('dashboard.orders.preparation_time');
Route::post('/admin/orders/preparation-time/{order}', [UserProfileController::class, 'updatePreparationTime'])->name('dashboard.orders.update_preparation_time');


Route::get('/DashboardV2', [DashboardController::class, 'index'])->name('dashboard');



Route::get('/coupon', [CouponController::class, 'indexDash'])->name('coupon');
Route::post('/coupon/create', [CouponController::class, 'createCouponDash'])->name('coupon.create_coupon');




Route::prefix('chef-management')->name('chef_management.')->group(function () {
    Route::get('/', [ChefController::class, 'index'])->name('index');
    Route::get('/create', [ChefController::class, 'create'])->name('create');
    Route::post('/store', [ChefController::class, 'store'])->name('store');
    Route::delete('/{chef}', [ChefController::class, 'destroy'])->name('destroy');
});

//userMoreData
Route::get('/DashboardV2/userMoreData', [UserProfileController::class, 'userMoreData'])->name('dashboard.userMoreData');

Route::get('/offers/create', [OfferController::class, 'NewCreate'])->name('offers.create');
Route::post('/offers', [OfferController::class, 'NewStore'])->name('offers.store.brand');


Route::get('/create-item', [ItemController::class, 'createNew'])->name('items.create');
Route::post('/items', [ItemController::class, 'storeNew'])->name('items.store');
Route::get('/edit/{id}', [ItemController::class, 'edit'])->name('items.edit');
Route::put('/update/{id}', [ItemController::class, 'update'])->name('items.update');
Route::get('/branch-index', [ItemController::class, 'branchIndex'])->name('Items.branch-index');
Route::get('/edit-brand/{id}', [ItemController::class, 'editBrand'])->name('items.edit-brand');
Route::put('/update-brand/{id}', [ItemController::class, 'updateBrand'])->name('itemsupdate-brand');
Route::get('/show/{id}', [ItemController::class, 'show'])->name('items.show');



Route::prefix('chef')->name('chef.')->group(function () {
    Route::get('/login', [ChefController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ChefController::class, 'login'])->name('login');
    Route::post('/logout', [ChefController::class, 'logout'])->name('logout')->middleware('auth:chef');
    Route::middleware(['auth:chef'])->group(function () {
        Route::get('/orders', [ChefController::class, 'showOrders'])->name('orders');
        Route::put('/orders/{order}/status', [ChefController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    });
});


Route::get('/branches', [BranchController::class, 'NewIndex'])->name('branches.index');
Route::get('/branches/{branch}/edit-brand', [BranchController::class, 'editBrand'])->name('branches.edit-brand');
Route::put('/branches/{branch}/update-brand', [BranchController::class, 'updateBrand'])->name('branches.update-brand');

Route::get('/areas/select-branch', [BranchController::class, 'selectBranchAreas'])->name('areas.select-branch');
Route::get('/areas/create', [BranchController::class, 'createAreas'])->name('areas.create');
Route::post('/areas/store', [BranchController::class, 'storeAreas'])->name('areas.store');
Route::get('/areas/index', [BranchController::class, 'indexAreas'])->name('areas.index');
Route::get('/areas/view-zones', [BranchController::class, 'viewZones'])->name('areas.view-zones');
Route::patch('/areas/{id}/update-status', [BranchController::class, 'updateStatusAreas'])->name('areas.update-status');
Route::patch('/areas/{id}/update-delivery', [BranchController::class, 'updateDeliveryAreas'])->name('areas.update-delivery');
Route::get('/areas/{id}/edit', [BranchController::class, 'editAreas'])->name('areas.edit');
Route::patch('/areas/{id}', [BranchController::class, 'updateAreas'])->name('areas.update');

Route::get('/brands', [BrandController::class, 'indexView'])->name('brands');
Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
Route::get('/brands/edit/{brand}', [BrandController::class, 'edit'])->name('brands.edit');
Route::post('/brands/update/{brand}', [BrandController::class, 'update'])->name('brands.update');
Route::delete('/brands/delete/{brand}', [BrandController::class, 'destroy'])->name('brands.delete');

Route::get('/items-relations', [ItemController::class, 'branchIndex'])->name('items.branch-index');
Route::get('/items/{item}/edit-brand', [ItemController::class, 'editBrand'])->name('items.edit-brand');
Route::put('/items/{item}/update-brand', [ItemController::class, 'updateBrand'])->name('items.update-brand');
Route::get('/items/{item}/edit-branches', [ItemController::class, 'editBranches'])->name('items.edit-branches');
Route::put('/items/{item}/update-branches', [ItemController::class, 'updateBranches'])->name('items.update-branches');

Route::get('/{any}', [RootController::class, 'index'])->middleware(['installed'])->where(['any' => '.*']);
