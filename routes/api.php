<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DeadlineController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LdapAuthController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\VehicleTypeController;
use App\Http\Controllers\Api\VehicleDocumentController;


Route::post('login', [AuthController::class, 'login']);
Route::post('/ldap-login', [LdapAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('isAdmin')->group(function () {
        Route::post('register', [AuthController::class, 'register']);

        Route::apiResource('groups', GroupController::class);
        Route::apiResource('documents', DocumentController::class);
        Route::post('employees/{employee}/assign-group', [AssignmentController::class, 'assignGroup']);
        Route::post('employees/{employee}/assign-groups', [AssignmentController::class, 'assignMultipleGroups']);
        Route::post('employees/{employee}/detach-group', [AssignmentController::class, 'detachGroup']);
        Route::post('groups/{group}/attach-documents', [GroupController::class, 'attachDocuments']);
        Route::post('groups/{group}/detach-documents', [GroupController::class, 'detachDocuments']);

        Route::post('/users/{user}/make-admin', [UserRoleController::class, 'makeAdmin']);
        Route::post('/users/{user}/remove-admin', [UserRoleController::class, 'removeAdmin']);

        Route::apiResource('tags', TagController::class);

        Route::apiResource('vehicle-types', VehicleTypeController::class);
    });
    
    Route::middleware('admin.or.ldap:GESTIONALE-Dipendenti')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::get('employees/{employee}/documents', [EmployeeController::class, 'getDocuments']);
        Route::get('employees/documents/overview', [EmployeeController::class, 'documentOverview']);
        Route::get('employees/documents/expiring', [EmployeeController::class, 'expiring']);
        Route::get('employees/documents/expired', [EmployeeController::class, 'expired']);
        Route::get('employees/{employee}/missing-documents', [EmployeeController::class, 'missingDocuments']);
        Route::get('employees/{employee}/documents/export', [EmployeeController::class, 'exportDocuments']);
        Route::get('employees/{employee}/documents/export/pdf', [EmployeeController::class, 'exportDocumentsPdf']);
        Route::get('documents/export/all', [EmployeeController::class, 'exportAllDocumentsExcel']);

        Route::post('employees/{employee}/assign-document', [AssignmentController::class, 'assignDocument']);
        Route::patch('employees/{employee}/documents/{document}', [AssignmentController::class, 'updateExpiration']);
        Route::post('employee-documents/{id}/attachments', [AssignmentController::class, 'uploadAttachments']);
        Route::post('employee-documents/{id}/images', [AssignmentController::class, 'uploadDocumentImages']);
        Route::get('employee-documents/{id}/images', [AssignmentController::class, 'getImages']);
        Route::delete('employee-documents/images/{id}', [AssignmentController::class, 'deleteImage']);

        Route::apiResource('attendances', AttendanceController::class);
        Route::apiResource('presences', PresenceController::class);

    });

    Route::middleware('admin.or.ldap:GESTIONALE-Scadenzario')->group(function () {
        Route::apiResource('deadlines', DeadlineController::class);
        Route::get('/deadlines/expired',   [DeadlineController::class,'expired']);
        Route::get('/deadlines/expiring',  [DeadlineController::class,'expiring']);
    });

    Route::middleware('admin.or.ldap:GESTIONALE-Flotta')->group(function () {
        Route::apiResource('vehicles', VehicleController::class);

        Route::post('vehicles/{vehicle}/documents',  [VehicleDocumentController::class,'store']);
        Route::patch('vehicle-documents/{vehicleDocument}', [VehicleDocumentController::class,'update']);
        Route::delete('vehicle-documents/{vehicleDocument}',[VehicleDocumentController::class,'destroy']);
    });
});