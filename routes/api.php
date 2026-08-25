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
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\KanbanController;

// Health Check (public)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
    ]);
});

// ==================== API V1 ROUTES ====================
Route::prefix('v1')->group(function () {
    
    // Auth Routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    
    // Auth Routes (protected)
    Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/update', [AuthController::class, 'update']);
    });
    
    // ==================== PROTECTED ROUTES ====================
    Route::middleware('auth:sanctum')->group(function () {
        
        // Subscription
        Route::get('/subscription/current', [SubscriptionController::class, 'current']);
        Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
        Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade']);
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel']);
        
        // Companies
        Route::prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::post('/', [CompanyController::class, 'store']);
            Route::get('/{company}', [CompanyController::class, 'show']);
            Route::put('/{company}', [CompanyController::class, 'update']);
            Route::delete('/{company}', [CompanyController::class, 'destroy']);
            Route::post('/{company}/activate', [CompanyController::class, 'activate']);
            Route::post('/{company}/suspend', [CompanyController::class, 'suspend']);
            Route::get('/{company}/users', [CompanyController::class, 'users']);
        });
        
        // Users
        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::post('/', [UserManagementController::class, 'store']);
            Route::get('/{user}', [UserManagementController::class, 'show']);
            Route::put('/{user}', [UserManagementController::class, 'update']);
            Route::delete('/{user}', [UserManagementController::class, 'destroy']);
            Route::post('/{user}/activate', [UserManagementController::class, 'activate']);
            Route::post('/{user}/deactivate', [UserManagementController::class, 'deactivate']);
        });
        
        // Projects
        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::get('/{project}', [ProjectController::class, 'show']);
            Route::put('/{project}', [ProjectController::class, 'update']);
            Route::delete('/{project}', [ProjectController::class, 'destroy']);
            Route::get('/{project}/tasks', [ProjectController::class, 'tasks']);
        });
        
        // Tasks
        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{id}', [TaskController::class, 'show']);
            Route::put('/{id}', [TaskController::class, 'update']);
            Route::delete('/{id}', [TaskController::class, 'destroy']);
            Route::post('/{id}/status', [TaskController::class, 'updateStatus']);
        });
        
        // Teams
        Route::prefix('teams')->group(function () {
            Route::get('/', [TeamController::class, 'index']);
            Route::post('/', [TeamController::class, 'store']);
            Route::get('/{id}', [TeamController::class, 'show']);
            Route::put('/{id}', [TeamController::class, 'update']);
            Route::delete('/{id}', [TeamController::class, 'destroy']);
            Route::post('/{id}/members', [TeamController::class, 'addMember']);
            Route::delete('/{id}/members/{user}', [TeamController::class, 'removeMember']);
        });
        
        // Calendar
        Route::prefix('calendar')->group(function () {
            Route::get('/', [CalendarController::class, 'index']);
            Route::post('/', [CalendarController::class, 'store']);
            Route::get('/{id}', [CalendarController::class, 'show']);
            Route::put('/{id}', [CalendarController::class, 'update']);
            Route::delete('/{id}', [CalendarController::class, 'destroy']);
        });
        
        // Time Tracking
        Route::prefix('time-tracking')->group(function () {
            Route::get('/', [TimeTrackingController::class, 'index']);
            Route::post('/', [TimeTrackingController::class, 'store']);
        });
        
        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/overview', [ReportsController::class, 'overview']);
            Route::get('/project', [ReportsController::class, 'projectReport']);
            Route::get('/team-performance', [ReportsController::class, 'teamPerformance']);
            Route::get('/financial', [ReportsController::class, 'financialReport']);
            Route::get('/productivity', [ReportsController::class, 'productivityReport']);
            Route::get('/budget-vs-actual', [ReportsController::class, 'budgetVsActual']);
        });
        
        // Finance
        Route::prefix('finance')->group(function () {
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
        Route::post('/team/invite', [TeamInviteController::class, 'invite']);
        
        // ==================== KANBAN ROUTES ====================
        Route::prefix('kanban')->group(function () {
            Route::get('/{projectId}', [KanbanController::class, 'getBoard']);
            Route::post('/move', [KanbanController::class, 'moveTask']);
            Route::post('/columns', [KanbanController::class, 'createColumn']);
            Route::put('/columns/{id}', [KanbanController::class, 'updateColumn']);
            Route::delete('/columns/{id}', [KanbanController::class, 'deleteColumn']);
  
      });
// Sprint Routes
Route::prefix('sprints')->group(function () {
    Route::get('/project/{projectId}', [App\Http\Controllers\Api\V1\SprintController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\V1\SprintController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\V1\SprintController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\V1\SprintController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\V1\SprintController::class, 'destroy']);
    Route::post('/{id}/start', [App\Http\Controllers\Api\V1\SprintController::class, 'start']);
    Route::post('/{id}/complete', [App\Http\Controllers\Api\V1\SprintController::class, 'complete']);
    Route::post('/{id}/add-task', [App\Http\Controllers\Api\V1\SprintController::class, 'addTask']);
    Route::delete('/{sprintId}/remove-task/{taskId}', [App\Http\Controllers\Api\V1\SprintController::class, 'removeTask']);
});


// Finance Routes
Route::prefix('finance')->group(function () {
    Route::get('/budget/{projectId}', [App\Http\Controllers\Api\V1\FinanceController::class, 'getBudget']);
    Route::post('/budget', [App\Http\Controllers\Api\V1\FinanceController::class, 'createBudget']);
    Route::put('/budget/{id}', [App\Http\Controllers\Api\V1\FinanceController::class, 'updateBudget']);
    Route::get('/expenses/{projectId}', [App\Http\Controllers\Api\V1\FinanceController::class, 'getExpenses']);
    Route::post('/expense', [App\Http\Controllers\Api\V1\FinanceController::class, 'createExpense']);
    Route::post('/expense/{id}/approve', [App\Http\Controllers\Api\V1\FinanceController::class, 'approveExpense']);
    Route::get('/invoices/{projectId}', [App\Http\Controllers\Api\V1\FinanceController::class, 'getInvoices']);
    Route::post('/invoice', [App\Http\Controllers\Api\V1\FinanceController::class, 'createInvoice']);
    Route::post('/invoice/{id}/pay', [App\Http\Controllers\Api\V1\FinanceController::class, 'markInvoicePaid']);
    Route::get('/dashboard/{projectId}', [App\Http\Controllers\Api\V1\FinanceController::class, 'getDashboard']);
});

// Report Routes
Route::prefix('reports')->group(function () {
    Route::get('/overview', [App\Http\Controllers\Api\V1\ReportsController::class, 'overview']);
    Route::get('/project', [App\Http\Controllers\Api\V1\ReportsController::class, 'projectReport']);
    Route::get('/team-performance', [App\Http\Controllers\Api\V1\ReportsController::class, 'teamPerformance']);
    Route::get('/financial', [App\Http\Controllers\Api\V1\ReportsController::class, 'financialReport']);
    Route::get('/productivity', [App\Http\Controllers\Api\V1\ReportsController::class, 'productivityReport']);
    Route::get('/budget-vs-actual', [App\Http\Controllers\Api\V1\ReportsController::class, 'budgetVsActual']);
});
// 2FA Routes (Enterprise)
Route::prefix('2fa')->group(function () {
    Route::post('/enable', [App\Http\Controllers\Api\V1\TwoFactorController::class, 'enable']);
    Route::post('/verify', [App\Http\Controllers\Api\V1\TwoFactorController::class, 'verify']);
    Route::post('/disable', [App\Http\Controllers\Api\V1\TwoFactorController::class, 'disable']);
});
// Portfolio Routes (Enterprise)
Route::prefix('portfolio')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\V1\PortfolioController::class, 'index']);
});


    });

});
