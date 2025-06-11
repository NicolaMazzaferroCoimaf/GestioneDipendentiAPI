<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AssignmentController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('groups', GroupController::class);
        Route::apiResource('documents', DocumentController::class);
        Route::post('employees/{employee}/assign-group', [AssignmentController::class, 'assignGroup']);
        Route::post('employees/{employee}/assign-groups', [AssignmentController::class, 'assignMultipleGroups']);
        Route::post('employees/{employee}/detach-group', [AssignmentController::class, 'detachGroup']);
        Route::post('groups/{group}/assign-documents', [GroupController::class, 'assignDocuments']);
        Route::post('groups/{group}/detach-documents', [GroupController::class, 'detachDocuments']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('employees/{employee}/documents', [EmployeeController::class, 'getDocuments']);
        Route::patch('employees/{employee}/documents/{document}', [AssignmentController::class, 'updateExpiration']);
        Route::post('employee-documents/{id}/attachments', [AssignmentController::class, 'uploadAttachments']);
        Route::post('employee-documents/{id}/images', [AssignmentController::class, 'uploadDocumentImages']);
        Route::get('employee-documents/{id}/images', [AssignmentController::class, 'getImages']);
        Route::delete('employee-documents/images/{id}', [AssignmentController::class, 'deleteImage']);
        Route::get('employees/documents/expiring', [EmployeeController::class, 'expiring']);
        Route::get('employees/documents/expired', [EmployeeController::class, 'expired']);
        Route::get('employees/{employee}/missing-documents', [EmployeeController::class, 'missingDocuments']);
        Route::get('employees/{employee}/documents/export', [EmployeeController::class, 'exportDocuments']);
        Route::get('employees/{employee}/documents/export/pdf', [EmployeeController::class, 'exportDocumentsPdf']);
        Route::get('documents/export/all', [EmployeeController::class, 'exportAllDocumentsExcel']);


        Route::post('logout', [AuthController::class, 'logout']);
    });
});