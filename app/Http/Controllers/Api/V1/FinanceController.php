<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    // Test method
    public function test()
    {
        return response()->json(['message' => 'Finance controller works!']);
    }

    // ==================== BUDGET ====================
    public function getBudget($projectId)
    {
        try {
            $budget = DB::table('budgets')->where('project_id', $projectId)->first();
            if (!$budget) {
                return response()->json(['message' => 'Budget not found'], 404);
            }
            return response()->json($budget);
        } catch (\Exception $e) {
            Log::error('getBudget error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createBudget(Request $request)
    {
        try {
            Log::info('createBudget called', $request->all());
            
            $id = DB::table('budgets')->insertGetId([
                'project_id' => $request->project_id,
                'total_budget' => $request->total_budget,
                'currency' => $request->currency ?? 'EUR',
                'spent' => 0,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $budget = DB::table('budgets')->where('id', $id)->first();
            return response()->json($budget, 201);
        } catch (\Exception $e) {
            Log::error('createBudget error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateBudget(Request $request, $id)
    {
        try {
            DB::table('budgets')->where('id', $id)->update([
                'total_budget' => $request->total_budget,
                'currency' => $request->currency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'updated_at' => now(),
            ]);

            $budget = DB::table('budgets')->where('id', $id)->first();
            return response()->json($budget);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================== EXPENSES ====================
    public function getExpenses($projectId)
    {
        try {
            $expenses = DB::table('expenses')
                ->where('project_id', $projectId)
                ->orderBy('date', 'desc')
                ->get();
            return response()->json($expenses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createExpense(Request $request)
    {
        try {
            $id = DB::table('expenses')->insertGetId([
                'project_id' => $request->project_id,
                'user_id' => Auth::id(),
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date ?? date('Y-m-d'),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $expense = DB::table('expenses')->where('id', $id)->first();
            return response()->json($expense, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approveExpense($id)
    {
        try {
            DB::table('expenses')->where('id', $id)->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $expense = DB::table('expenses')->where('id', $id)->first();
            return response()->json($expense);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================== INVOICES ====================
    public function getInvoices($projectId)
    {
        try {
            $invoices = DB::table('invoices')
                ->where('project_id', $projectId)
                ->orderBy('issue_date', 'desc')
                ->get();
            return response()->json($invoices);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createInvoice(Request $request)
    {
        try {
            $count = DB::table('invoices')->count() + 1;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $id = DB::table('invoices')->insertGetId([
                'project_id' => $request->project_id,
                'invoice_number' => $invoiceNumber,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'amount' => $request->amount,
                'tax' => $request->tax ?? 0,
                'total' => $request->amount + ($request->tax ?? 0),
                'issue_date' => $request->issue_date ?? date('Y-m-d'),
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $invoice = DB::table('invoices')->where('id', $id)->first();
            return response()->json($invoice, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markInvoicePaid($id)
    {
        try {
            DB::table('invoices')->where('id', $id)->update([
                'status' => 'paid',
                'paid_date' => now(),
            ]);

            $invoice = DB::table('invoices')->where('id', $id)->first();
            return response()->json($invoice);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================== DASHBOARD ====================
    public function getDashboard($projectId)
    {
        try {
            $budget = DB::table('budgets')->where('project_id', $projectId)->first();
            $expenses = DB::table('expenses')->where('project_id', $projectId)->get();
            $invoices = DB::table('invoices')->where('project_id', $projectId)->get();

            return response()->json([
                'budget' => $budget,
                'total_expenses' => $expenses->sum('amount'),
                'total_invoiced' => $invoices->where('status', 'paid')->sum('total'),
                'pending_invoices' => $invoices->where('status', '!=', 'paid')->sum('total'),
                'remaining_budget' => $budget ? $budget->total_budget - $expenses->sum('amount') : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
