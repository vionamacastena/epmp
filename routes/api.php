
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\UserManagementController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\TimeTrackingController;
use App\Http\Controllers\Api\V1\ReportsController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\TeamInviteController;
Route::put('/auth/update', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {
    
    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is running',
            'timestamp' => now()->toISOString(),
        ]);
    });

    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

    // Company Routes
    Route::prefix('companies')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'destroy']);
        Route::post('/{company}/activate', [CompanyController::class, 'activate']);
        Route::post('/{company}/suspend', [CompanyController::class, 'suspend']);
        Route::get('/{company}/users', [CompanyController::class, 'users']);
    });

    // User Routes
    Route::prefix('users')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::post('/', [UserManagementController::class, 'store']);
        Route::get('/{user}', [UserManagementController::class, 'show']);
        Route::put('/{user}', [UserManagementController::class, 'update']);
        Route::delete('/{user}', [UserManagementController::class, 'destroy']);
        Route::post('/{user}/activate', [UserManagementController::class, 'activate']);
        Route::post('/{user}/deactivate', [UserManagementController::class, 'deactivate']);
    });

    // Project Routes
    Route::prefix('projects')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{project}', [ProjectController::class, 'show']);
        Route::put('/{project}', [ProjectController::class, 'update']);
        Route::delete('/{project}', [ProjectController::class, 'destroy']);
        Route::get('/{project}/tasks', [ProjectController::class, 'tasks']);
    });

    // Task Routes
Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{id}', [TaskController::class, 'show']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::delete('/{id}', [TaskController::class, 'destroy']);
    Route::post('/{id}/status', [TaskController::class, 'updateStatus']);
});

    // Team Routes
Route::prefix('teams')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::post('/', [TeamController::class, 'store']);
    Route::get('/{id}', [TeamController::class, 'show']);
    Route::put('/{id}', [TeamController::class, 'update']);
    Route::delete('/{id}', [TeamController::class, 'destroy']);
    Route::post('/{id}/members', [TeamController::class, 'addMember']);
    Route::delete('/{id}/members/{user}', [TeamController::class, 'removeMember']);
});

    // Calendar Routes
    Route::prefix('calendar')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [CalendarController::class, 'index']);
        Route::post('/', [CalendarController::class, 'store']);
        Route::get('/{id}', [CalendarController::class, 'show']);
        Route::put('/{id}', [CalendarController::class, 'update']);
        Route::delete('/{id}', [CalendarController::class, 'destroy']);
    });

    // Time Tracking Routes
    Route::prefix('time-tracking')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [TimeTrackingController::class, 'index']);
        Route::post('/', [TimeTrackingController::class, 'store']);
    });

    // Reports Routes
    Route::prefix('reports')->middleware('auth:sanctum')->group(function () {
        Route::get('/overview', [ReportsController::class, 'overview']);
        Route::get('/project', [ReportsController::class, 'projectReport']);
        Route::get('/team-performance', [ReportsController::class, 'teamPerformance']);
        Route::get('/financial', [ReportsController::class, 'financialReport']);
        Route::get('/productivity', [ReportsController::class, 'productivityReport']);
        Route::get('/budget-vs-actual', [ReportsController::class, 'budgetVsActual']);
    });

    // Finance Routes
    Route::prefix('finance')->middleware('auth:sanctum')->group(function () {
        Route::get('/budget/{projectId}', [FinanceController::class, 'getBudget']);
        Route::post('/budget', [FinanceController::class, 'createBudget']);
        Route::put('/budget/{id}', [FinanceController::class, 'updateBudget']);
        Route::get('/expenses/{projectId}', [FinanceController::class, 'getExpenses']);
        Route::post('/expense', [FinanceController::class, 'createExpense']);
        Route::post('/expense/{id}/approve', [FinanceController::class, 'approveExpense']);
        Route::get('/invoices/{projectId}', [FinanceController::class, 'getInvoices']);
        Route::post('/invoice', [FinanceController::class, 'createInvoice']);
        Route::post('/invoice/{id}/pay', [FinanceController::class, 'markInvoicePaid']);
        Route::get('/dashboard/{projectId}', [FinanceController::class, 'getDashboard']);
    });

    // Team Invite
    Route::post('/team/invite', [TeamInviteController::class, 'invite'])->middleware('auth:sanctum');
});


// Task Routes
Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{id}', [TaskController::class, 'show']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::delete('/{id}', [TaskController::class, 'destroy']);
});

