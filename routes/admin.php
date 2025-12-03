<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AssignCourierController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\BookingOperatorController;
use App\Http\Controllers\Backend\BulkUploadController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DeliveryTypeController;
use App\Http\Controllers\Backend\DispatchInchargeController;
use App\Http\Controllers\Backend\DistrictController;
use App\Http\Controllers\Backend\DivisionController;
use App\Http\Controllers\Backend\EmailVerifyController;
use App\Http\Controllers\Backend\MerchantController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductManageController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\StoreAdminController;
use App\Http\Controllers\Backend\StoreController;
use App\Http\Controllers\Backend\StoreManageController;
use App\Http\Controllers\Backend\HubController;
use App\Http\Controllers\Backend\HubInchargeController;
use App\Http\Controllers\Backend\StockMovementController;
use App\Http\Controllers\Backend\StoreInchargeController;
use App\Http\Controllers\Backend\KYCController;
use App\Http\Controllers\Backend\PathaoController;
use App\Http\Controllers\Backend\PhoneVerifyController;
use App\Http\Controllers\Backend\ProductTypeController;
use App\Http\Controllers\Backend\ThanaController;
use Illuminate\Support\Facades\Route;


## Protected Route @ Admin Dashboard Home
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

### Protected Route @ Admin Profile & Password Pages Start
Route::middleware(['auth'])->group(function () {

    ## Profile & Password Pages
    Route::get('admin/profile/page', [AdminController::class, 'AdminProfilePage'])->name('admin.profile.page');
    Route::get('admin/password/change/page', [AdminController::class, 'AdminPasswordChangePage'])->name('admin.password.change.page');

    ## Profile & Password Update
    Route::post('admin/profile/update', [AdminController::class, 'AdminProfileUpdate'])->name('admin.profile.update');
    Route::post('admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');

    ## Merchant Registration
    Route::get('admin/register/merchant/page', [MerchantController::class, 'manageMerchant'])->name('admin.register.merchant.page');
    Route::get('admin/status/merchant/toggle/{id}', [MerchantController::class, 'toggleStatus'])->name('admin.toggle.merchant.status');

    ## Store
    Route::get('admin/store/index/{id}', [StoreController::class, 'index'])->name('admin.store.index');
    Route::get('admin/store/add/{id}', [StoreController::class, 'add'])->name('admin.store.add');
    Route::get('admin/store/edit/{id}', [StoreController::class, 'edit'])->name('admin.store.edit');
    Route::post('admin/store/store', [StoreController::class, 'store'])->name('admin.store.store');
    Route::post('admin/store/update/{id}', [StoreController::class, 'update'])->name('admin.store.update');
    // Route::post('admin/store/destroy/{id}', [StoreController::class, 'destroy'])->name('admin.store.destroy');
    Route::post('admin/store/assign/{id}', [StoreController::class, 'assignStoreAdmin'])->name('admin.store.assign');
    Route::get('admin/store/status/toggle/{id}', [StoreController::class, 'toggleStatus'])->name('admin.store.toggle.status');
    Route::get('admin/store/page', [StoreController::class, 'show'])->name('admin.store.show');

    ## Store Admin
    Route::get('admin/store-admin/index', [StoreAdminController::class, 'index'])->name('admin.store.admin.index');
    Route::post('admin/store-admin/store', [StoreAdminController::class, 'store'])->name('admin.store.admin.store');

    ## Store Manage
    Route::get('admin/store-manage/index', [StoreManageController::class, 'index'])->name('admin.store.manage.index');
    Route::post('admin/store-manage/store', [StoreManageController::class, 'store'])->name('admin.store.manage.store');

    ## Product
    Route::get('admin/product/index', [ProductController::class, 'index'])->name('admin.product.index');
    Route::get('admin/product/create', [ProductController::class, 'create'])->name('admin.product.create');
    Route::get('admin/product/edit/{id}', [ProductController::class, 'edit'])->name('admin.product.edit');
    Route::post('admin/product/store', [ProductController::class, 'store'])->name('admin.product.store');
    Route::post('admin/product/update/{id}', [ProductController::class, 'update'])->name('admin.product.update');

    ## Product Manage
    Route::get('admin/product-manage/index/{id}', [ProductManageController::class, 'index'])->name('admin.product.manage.index');
    Route::get('admin/product-manage/add/{id}', [ProductManageController::class, 'add'])->name('admin.product.manage.add');

    ## New Owner Registration
    Route::get('admin/register/page', [AdminController::class, 'RegisterNewMember'])->name('admin.register.page');
    Route::post('admin/register/store', [AdminController::class, 'store'])->name('admin.register.store');
    Route::get('admin/status/toggle/{id}', [AdminController::class, 'toggleStatus'])->name('admin.toggle.status');

    ## New Memember Registration
    Route::get('admin/register/page', [AdminController::class, 'RegisterNewMember'])->name('admin.register.page');
    Route::post('admin/register/store', [AdminController::class, 'store'])->name('admin.register.store');
    Route::get('admin/status/toggle/{id}', [AdminController::class, 'toggleStatus'])->name('admin.toggle.status');

    ## Category
    Route::get('admin/category/index', [CategoryController::class, 'index'])->name('admin.category.index');
    Route::get('admin/category/create', [CategoryController::class, 'create'])->name('admin.category.create');
    Route::get('admin/category/edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit');
    Route::post('admin/category/store', [CategoryController::class, 'store'])->name('admin.category.store');
    Route::post('admin/category/update/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
    Route::post('admin/category/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
    Route::get('admin/category/status/toggle/{id}', [CategoryController::class, 'toggleStatus'])->name('admin.category.toggle.status');

    ## Hub
    Route::get('admin/hub/index', [HubController::class, 'index'])->name('admin.hub.index');
    Route::get('admin/hub/create', [HubController::class, 'create'])->name('admin.hub.create');
    Route::get('admin/hub/edit/{id}', [HubController::class, 'edit'])->name('admin.hub.edit');
    Route::post('admin/hub/store', [HubController::class, 'store'])->name('admin.hub.store');
    Route::post('admin/hub/update/{id}', [HubController::class, 'update'])->name('admin.hub.update');
    Route::post('admin/hub/destroy/{id}', [HubController::class, 'destroy'])->name('admin.hub.destroy');
    Route::get('admin/hub/status/toggle/{id}', [HubController::class, 'toggleStatus'])->name('admin.hub.toggle.status');

    ## Hub Incharge
    Route::get('admin/hub/incharge/index', [HubInchargeController::class, 'index'])->name('admin.hub.inchage.index');
    Route::get('admin/hub/incharge/create', [HubInchargeController::class, 'create'])->name('admin.hub.inchage.create');
    Route::get('admin/hub/incharge/edit/{id}', [HubInchargeController::class, 'edit'])->name('admin.hub.inchage.edit');
    Route::post('admin/hub/incharge/store', [HubInchargeController::class, 'store'])->name('admin.hub.inchage.store');
    Route::post('admin/hub/incharge/update/{id}', [HubInchargeController::class, 'update'])->name('admin.hub.inchage.update');
    Route::post('admin/hub/incharge/destroy/{id}', [HubInchargeController::class, 'destroy'])->name('admin.hub.inchage.destroy');
    Route::get('admin/hub/incharge/status/toggle/{id}', [HubInchargeController::class, 'toggleStatus'])->name('admin.hub.inchage.toggle.status');

    ## Store Incharge
    Route::get('admin/store/incharge/index', [StoreInchargeController::class, 'index'])->name('admin.store.inchage.index');
    Route::get('admin/store/incharge/create', [StoreInchargeController::class, 'create'])->name('admin.store.inchage.create');
    Route::get('admin/store/incharge/edit/{id}', [StoreInchargeController::class, 'edit'])->name('admin.store.inchage.edit');
    Route::post('admin/store/incharge/store', [StoreInchargeController::class, 'store'])->name('admin.store.inchage.store');
    Route::post('admin/store/incharge/update/{id}', [StoreInchargeController::class, 'update'])->name('admin.store.inchage.update');
    Route::post('admin/store/incharge/destroy/{id}', [StoreInchargeController::class, 'destroy'])->name('admin.store.inchage.destroy');
    Route::get('admin/store/incharge/status/toggle/{id}', [StoreInchargeController::class, 'toggleStatus'])->name('admin.store.inchage.toggle.status');

    ## Dispatch Incharge
    Route::get('admin/dispatch/incharge/index', [DispatchInchargeController::class, 'index'])->name('admin.dispatch.incharge.index');
    Route::get('admin/dispatch/incharge/create', [DispatchInchargeController::class, 'create'])->name('admin.dispatch.incharge.create');
    Route::get('admin/dispatch/incharge/edit/{id}', [DispatchInchargeController::class, 'edit'])->name('admin.dispatch.incharge.edit');
    Route::post('admin/dispatch/incharge/store', [DispatchInchargeController::class, 'store'])->name('admin.dispatch.incharge.store');
    Route::post('admin/dispatch/incharge/update/{id}', [DispatchInchargeController::class, 'update'])->name('admin.dispatch.incharge.update');
    Route::post('admin/dispatch/incharge/destroy/{id}', [DispatchInchargeController::class, 'destroy'])->name('admin.dispatch.incharge.destroy');
    Route::get('admin/dispatch/incharge/status/toggle/{id}', [DispatchInchargeController::class, 'toggleStatus'])->name('admin.dispatch.incharge.toggle.status');

    ## Booking Operator Registration
    Route::get('admin/booking/operator/page', [BookingOperatorController::class, 'index'])->name('admin.booking.operator.page');
    Route::get('admin/booking/operator/create', [BookingOperatorController::class, 'create'])->name('admin.booking.operator.create');
    Route::get('admin/booking/operator/edit/{id}', [BookingOperatorController::class, 'edit'])->name('admin.booking.operator.edit');
    Route::post('admin/booking/operator/operatornt/store', [BookingOperatorController::class, 'store'])->name('admin.booking.operator.store');
    Route::get('admin/status/booking/operator/toggle/{id}', [BookingOperatorController::class, 'toggleStatus'])->name('admin.toggle.booking.operator.status');

    ## Stock Movement
    Route::get('admin/stock/movement/page/{id}', [StockMovementController::class, 'index'])->name('admin.stock.movement.page');
    Route::post('admin/stock/movement/store', [StockMovementController::class, 'store'])->name('admin.stock.movement.store');

    ## kyc verification
    Route::get('admin/kyc/verification/page', [KYCController::class, 'index'])->name('admin.kyc.verification.page');
    Route::post('admin/kyc/verification/store', [KYCController::class, 'store'])->name('admin.kyc.verification.store');
    Route::post('admin/kyc/payment/details/store', [KYCController::class, 'PaymentDetailStore'])->name('admin.kyc.payment.details.store');

    # verify email and phone
    Route::post('admin/email/verify', [EmailVerifyController::class, 'emailVerify'])->name('admin.email.verify');
    Route::post('admin/phone/verify', [PhoneVerifyController::class, 'phoneVerify'])->name('admin.phone.verify');

    ## Booking
    Route::get('admin/booking/page', [BookingController::class, 'index'])->name('admin.booking.page');
    Route::get('admin/booking/create/page', [BookingController::class, 'create'])->name('admin.booking.create.page');
    Route::get('admin/booking/{id}/edit/page', [BookingController::class, 'edit'])->name('admin.booking.edit.page');

    Route::get('admin/booking/{id}/product/delete', [BookingController::class, 'deleteBookingProduct'])->name('admin.booking.product.delete.link');
    Route::post('admin/booking/{id}/product/edit/page', [BookingController::class, 'editBookingProduct'])->name('admin.booking.product.edit.page');
    Route::post('/admin/booking/{id}/update', [BookingController::class, 'updateBooking'])->name('admin.booking.update');














    // Route::get('admin/booking/create/product/page', [BookingController::class, 'addProduct'])->name('admin.add.booking.create.page');
    Route::post('admin/booking/store', [BookingController::class, 'store'])->name('admin.booking.store');
    // Route::get('admin/bookings/{orderId}/products', [BookingController::class, 'addProduct'])->name('admin.booking.products.store');bookingIndex
    Route::get('admin/bookings/{orderId}/products', [BookingController::class, 'bookingIndex'])->name('admin.booking.product.page');
    Route::post('admin/bookings/order/product/store', [BookingController::class, 'addProduct'])->name('admin.booking.product.store');
    Route::get('admin/bookings/order/product/delete/{id}', [BookingController::class, 'destroy'])->name('admin.booking.product.delete');

    ## Assign Courier Services
    Route::get('admin/assign/courier/services/page', [AssignCourierController::class, 'index'])->name('admin.assign.courier.services.page');
    Route::post('admin/assign/courier/services', [AssignCourierController::class, 'order'])->name('admin.assign.courier.services');
    Route::get('admin/assign/courier/services/invoice/{id}/page', [AssignCourierController::class, 'invoice'])->name('admin.assign.courier.services.invoice.page');

    ## Product Type
    Route::get('admin/product/type/index', [ProductTypeController::class, 'index'])->name('admin.product.type.index');
    Route::get('admin/product/type/create', [ProductTypeController::class, 'create'])->name('admin.product.type.create');
    Route::get('admin/product/type/edit/{id}', [ProductTypeController::class, 'edit'])->name('admin.product.type.edit');
    Route::post('admin/product/type/store', [ProductTypeController::class, 'store'])->name('admin.product.type.store');
    Route::post('admin/product/type/update/{id}', [ProductTypeController::class, 'update'])->name('admin.product.type.update');
    Route::get('admin/product/type/delete/{id}', [ProductTypeController::class, 'destroy'])->name('admin.product.type.delete');
    Route::get('admin/product/type/status/toggle/{id}', [ProductTypeController::class, 'toggleStatus'])->name('admin.product.type.toggle.status');

    ## Delivery Type
    Route::get('admin/delivery/type/index', [DeliveryTypeController::class, 'index'])->name('admin.delivery.type.index');
    Route::get('admin/delivery/type/create', [DeliveryTypeController::class, 'create'])->name('admin.delivery.type.create');
    Route::get('admin/delivery/type/edit/{id}', [DeliveryTypeController::class, 'edit'])->name('admin.delivery.type.edit');
    Route::post('admin/delivery/type/store', [DeliveryTypeController::class, 'store'])->name('admin.delivery.type.store');
    Route::post('admin/delivery/type/update/{id}', [DeliveryTypeController::class, 'update'])->name('admin.delivery.type.update');
    Route::get('admin/delivery/type/delete/{id}', [DeliveryTypeController::class, 'destroy'])->name('admin.delivery.type.delete');
    Route::get('admin/delivery/type/status/toggle/{id}', [DeliveryTypeController::class, 'toggleStatus'])->name('admin.delivery.type.toggle.status');


    Route::get('/pathao/cities', [PathaoController::class, 'getCities']);
    Route::get('/pathao/areas/{city_id}', [PathaoController::class, 'getAreas']);
    Route::get('/pathao/zones/{area_id}', [PathaoController::class, 'getZones']);
});
### Protected Route Admin Profile & Password Pages End




###################### Frontend Route
###################### Frontend Route
###################### Frontend Route



// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';




###################### Admin Route
###################### Admin Route
###################### Admin Route

## SiteSetting
Route::get('/settings/create', [SiteSettingController::class, 'create'])->name('settings.create');
Route::post('/settings/update', [SiteSettingController::class, 'update'])->name('settings.update');



## Division
Route::get('admin/division/index', [DivisionController::class, 'index'])->name('admin.division.index');
Route::get('admin/division/create', [DivisionController::class, 'create'])->name('admin.division.create');
Route::get('admin/division/edit/{id}', [DivisionController::class, 'edit'])->name('admin.division.edit');
Route::post('admin/division/store', [DivisionController::class, 'store'])->name('admin.division.store');
Route::post('admin/division/update/{id}', [DivisionController::class, 'update'])->name('admin.division.update');
Route::get('admin/division/delete/{id}', [DivisionController::class, 'destroy'])->name('admin.division.delete');
Route::get('admin/division/status/toggle/{id}', [DivisionController::class, 'toggleStatus'])->name('admin.division.toggle.status');

## District
Route::get('admin/district/index', [DistrictController::class, 'index'])->name('admin.district.index');
Route::get('admin/district/create', [DistrictController::class, 'create'])->name('admin.district.create');
Route::get('admin/district/edit/{id}', [DistrictController::class, 'edit'])->name('admin.district.edit');
Route::post('admin/district/store', [DistrictController::class, 'store'])->name('admin.district.store');
Route::post('admin/district/update/{id}', [DistrictController::class, 'update'])->name('admin.district.update');
Route::get('admin/district/delete/{id}', [DistrictController::class, 'destroy'])->name('admin.district.delete');
Route::get('admin/district/status/toggle/{id}', [DistrictController::class, 'toggleStatus'])->name('admin.district.toggle.status');

## Thana
Route::get('admin/thana/index', [ThanaController::class, 'index'])->name('admin.thana.index');
Route::get('admin/thana/create', [ThanaController::class, 'create'])->name('admin.thana.create');
Route::get('admin/thana/edit/{id}', [ThanaController::class, 'edit'])->name('admin.thana.edit');
Route::post('admin/thana/store', [ThanaController::class, 'store'])->name('admin.thana.store');
Route::post('admin/thana/update/{id}', [ThanaController::class, 'update'])->name('admin.thana.update');
Route::get('admin/thana/delete/{id}', [ThanaController::class, 'destroy'])->name('admin.thana.delete');
Route::get('admin/thana/status/toggle/{id}', [ThanaController::class, 'toggleStatus'])->name('admin.thana.toggle.status');



## Pathao Store
// Route::get('admin/pathao/store/index', [PathaoController::class, 'index'])->name('admin.pathao.store.index');
// Route::get('admin/pathao/store/create', [PathaoController::class, 'create'])->name('admin.pathao.store.create');
// Route::get('admin/pathao/store/edit/{id}', [PathaoController::class, 'edit'])->name('admin.pathao.store.edit');
// Route::post('admin/pathao/store/store', [PathaoController::class, 'store'])->name('admin.pathao.store.store');
// Route::post('admin/pathao/store/update/{id}', [PathaoController::class, 'update'])->name('admin.pathao.store.update');
// Route::get('admin/pathao/store/delete/{id}', [PathaoController::class, 'destroy'])->name('admin.pathao.store.delete');
// Route::get('admin/pathao/store/status/toggle/{id}', [PathaoController::class, 'toggleStatus'])->name('admin.pathao.store.toggle.status');



## Bulk Upload
Route::get('admin/bulk/upload/index', [BulkUploadController::class, 'index'])->name('admin.bulk.upload.index');
Route::post('admin/bulk/upload/store', [BulkUploadController::class, 'store'])->name('admin.bulk.upload.store');


// Permission All Route 
Route::controller(RoleController::class)->group(function () {

    Route::get('/all/permission', 'AllPermission')->name('all.permission');
    Route::get('/add/permission', 'AddPermission')->name('add.permission');
    Route::post('/store/permission', 'StorePermission')->name('store.permission');
    Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
    Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
    Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');
});

// Roles All Route 
Route::controller(RoleController::class)->group(function () {

    Route::get('/all/roles', 'AllRoles')->name('all.roles');
    Route::get('/add/roles', 'AddRoles')->name('add.roles');
    Route::post('/store/roles', 'StoreRoles')->name('store.roles');
    Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles');
    Route::post('/update/roles', 'UpdateRoles')->name('update.roles');
    Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles');

    // add role permission 

    Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
    Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');
    Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');
    Route::get('/admin/edit/roles/{id}', 'AdminRolesEdit')->name('admin.edit.roles');
    Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');
    Route::get('/admin/delete/roles/{id}', 'AdminRolesDelete')->name('admin.delete.roles');
});
