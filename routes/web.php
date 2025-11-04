<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\AgentAuthController;
use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\AgentLeadController;
use App\Http\Controllers\AgentCallLogController;
use App\Http\Controllers\Agent\Auth\ForgotPasswordController;
use App\Http\Controllers\Agent\Auth\ResetPasswordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Middleware\AgentAuth;
use App\Http\Controllers\AgentLocationController;
use App\Http\Controllers\AdminLocationController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');
Route::get('/', function () {
    return view('landing');
})->name('landing');
Route::get('/index', [LeadController::class, 'index'])->name('leads.index')->middleware('auth');
Route::get('/dashboard/leads/{status}', [DashboardController::class, 'leadsByStatus'])->name('dashboard.leadsByStatus');
Route::get('/leads', [LeadController::class, 'index'])->name('leads.index')->middleware('auth');
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create')->middleware('auth');
Route::get('/leads/transfer', [LeadController::class, 'transfer'])->name('leads.transfer')->middleware('auth');
Route::post('/leads/transfer', [LeadController::class, 'storeTransfer'])->name('leads.transfer.store')->middleware('auth');

//agent location routes in admin dashboard
Route::get('/admin-agent', [AgentLocationController::class, 'index'])->name('track.agent');

Route::get('/admin/agents/locations/latest', [AdminLocationController::class, 'getLatestLocations']);
Route::get('/admin/agents/location/{agentId}', [AdminLocationController::class, 'getAgentLocation']);


// Agents routes
Route::get('/agents', [AgentController::class, 'index'])->name('agents.index')->middleware('auth');
Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create')->middleware('auth');
Route::post('/agents', [AgentController::class, 'store'])->name('agents.store')->middleware('auth');
Route::get('/agents/performance', [AgentController::class, 'performance'])->name('agents.performance')->middleware('auth');
Route::get('/api/leads-by-agent/{agent_id}', [LeadController::class, 'getLeadsByAgent']);

Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
Route::get('/leads/{id}/edit', [LeadController::class, 'edit'])->name('leads.edit');
Route::put('/leads/{id}', [LeadController::class, 'update'])->name('leads.update');
Route::delete('/leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');
Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show'); // View agent
Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy'); // Delete agent
Route::get('/leads/update/status', [LeadController::class, 'updateStatusIndex'])->name('leads.updateStatusIndex');
Route::get('/leads/{id}/edit-status', [LeadController::class, 'editStatus'])->name('leads.editStatus');
Route::put('/leads/{id}/update-status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');
Route::get('/unassigned', [LeadController::class, 'showUnassignedLeads'])->name('leads.unassigned');
Route::post('/leads/unassigned/transfer', [LeadController::class, 'transferUnassignedLeads'])->name('leads.unassigned.transfer');

Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
Route::post('/loginaction', [AdminController::class, 'login'])->name('admin.login');
Route::get('/loginasp', [AgentController::class, 'assignLeadsToAgents'])->name('loginasp');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/agents/{agentId}/details', [AgentController::class, 'showAgentDetails'])->name('agents.details');
Route::get('/CallLogs/index', [CallLogController::class, 'index'])->name('callLogs.index');
Route::get('/transactions', [TransactionController::class, 'AdminIndex'])->name('transactions.index');


Route::prefix('agent')->group(function () {
    Route::get('login', [AgentAuthController::class, 'showLoginForm'])->name('agent.login');
    Route::post('login', [AgentAuthController::class, 'login'])->name('agent.login.submit');
    Route::post('logout', [AgentAuthController::class, 'logout'])->name('agent.logout');
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('agent.password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('agent.password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

     // Protect routes for agents only
     Route::middleware(AgentAuth::class)->group(function () {
        Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('agent.dashboard');
        Route::get('/leads', [AgentDashboardController::class, 'leads'])->name('agent.leads');
        Route::get('/call-logs', [AgentDashboardController::class, 'callindex'])->name('agent.callLogs');
        Route::get('/profile', [AgentDashboardController::class, 'show'])->name('agent.profile');
        Route::get('/profile/edit', [AgentDashboardController::class, 'edit'])->name('agent.profile.edit');
        Route::post('/profile/update', [AgentDashboardController::class, 'update'])->name('agent.profile.update');
        Route::get('/leads/{lead}', [AgentDashboardController::class, 'details'])->name('agent.lead.details');
        Route::get('/leads/{id}/edit', [AgentLeadController::class, 'edit'])->name('agent.leads.edit');
        Route::put('/leads/{id}', [AgentLeadController::class, 'update'])->name('agent.leads.update');
        Route::get('/lead/{id}/update-status', [AgentLeadController::class, 'showUpdateStatusForm'])->name('agent.lead.update-status');
        Route::post('/lead/{id}/update-status', [AgentLeadController::class, 'updateStatus'])->name('agent.lead.update-status.submit');
        Route::get('/calllogs/create', [AgentCallLogController::class, 'create'])->name('agent.calllog.create');
        Route::post('/calllogs', [AgentCallLogController::class, 'store'])->name('agent.calllog.store');
        Route::delete('/calllog/{id}', [AgentCallLogController::class, 'destroy'])->name('agent.calllog.delete');
        Route::get('/leads/{id}/set-followup', [AgentLeadController::class, 'setFollowUpForm'])->name('agent.lead.followup');
        Route::post('/leads/{id}/set-followup', [AgentLeadController::class, 'updateFollowUp'])->name('agent.lead.updateFollowUp');
        Route::get('/calllog/{id}/edit', [CallLogController::class, 'edit'])->name('agent.calllog.edit');
        Route::put('/calllog/{id}', [CallLogController::class, 'update'])->name('agent.calllog.update');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('agent.transactions');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('agent.transactions.create');
        Route::post('/transactions/store', [TransactionController::class, 'store'])->name('agent.transactions.store');
        Route::get('/lead/{id}/balance', [TransactionController::class, 'getLeadBalance'])->name('agent.lead.balance');
        Route::get('agent/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('agent.transactions.edit');
        Route::put('agent/transactions/{id}', [TransactionController::class, 'update'])->name('agent.transactions.update');
        Route::get('agents/leads/create', [AgentLeadController::class, 'create'])->name('agent.create.lead');
        Route::post('agents/leads/store', [AgentLeadController::class, 'store'])->name('agent.store.lead');

        Route::post('/location', [AgentLocationController::class, 'store'])->name('agent.location.store');
    // If you let agents read their history:
        Route::get('/location/history', [AgentLocationController::class, 'history']);
    });
});


Route::get('/send-followups', [AgentLeadController::class, 'sendTodayFollowUpsEmails'])->name('send.followups');
Route::get('/transactions/create', [AdminTransactionController::class, 'create'])->name('transactions.create');
Route::post('/transactions/store', [AdminTransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/get-lead-balance/{id}', [AdminTransactionController::class, 'getLeadBalance']);


    // Route::get('/admin/agents/{agent}/locations', [AgentLocationController::class, 'history']);
    // Route::view('/admin/agents/map', 'admin.agents_map')->name('admin.agents.map');
