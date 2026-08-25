<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    // ==================== BUDGET ====================
    
    public function getBudget($projectId)
    {
        try {
            $budget = Budget::where('project_id', $projectId)->first();
            
            if (!$budget) {
                return response()->json(['message' => 'Budget not found'], 404);
            }

            return response()->json([
                'data' => [
                    'budget' => $budget,
                    'spent' => $budget->getSpentAmount(),
                    'remaining' => $budget->getRemainingAmount(),
                    'utilization' => $budget->getUtilization(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function createBudget(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'amount' => 'required|numeric|min:0',
                'currency' => 'nullable|string|size:3',
            ]);

            $budget = Budget::create([
                'project_id' => $request->project_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'EUR',
                'allocated_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Budget created successfully',
                'data' => $budget
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateBudget(Request $request, $id)
    {
        try {
            $budget = Budget::find($id);
            if (!$budget) {
                return response()->json(['message' => 'Budget not found'], 404);
            }

            $budget->update($request->all());

            return response()->json([
                'message' => 'Budget updated successfully',
                'data' => $budget
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ==================== EXPENSES ====================

    public function getExpenses($projectId)
    {
        try {
            $expenses = Expense::where('project_id', $projectId)->orderBy('date', 'desc')->get();
            return response()->json(['data' => $expenses]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function createExpense(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'category' => 'required|string',
                'date' => 'required|date',
                'receipt' => 'nullable|string',
            ]);

            $expense = Expense::create([
                'project_id' => $request->project_id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'description' => $request->description,
                'category' => $request->category,
                'date' => $request->date,
                'receipt' => $request->receipt,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Expense created successfully',
                'data' => $expense
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function approveExpense(Request $request, $id)
    {
        try {
            $expense = Expense::find($id);
            if (!$expense) {
                return response()->json(['message' => 'Expense not found'], 404);
            }

            $expense->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Expense approved successfully',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ==================== INVOICES ====================

    public function getInvoices($projectId)
    {
        try {
            $invoices = Invoice::where('project_id', $projectId)->orderBy('issue_date', 'desc')->get();
            return response()->json(['data' => $invoices]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function createInvoice(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'client' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after:issue_date',
            ]);

            $total = $request->amount + ($request->tax ?? 0);

            $invoice = Invoice::create([
                'project_id' => $request->project_id,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
                'client' => $request->client,
                'amount' => $request->amount,
                'tax' => $request->tax ?? 0,
                'total' => $total,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function markInvoicePaid(Request $request, $id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            $invoice->update([
                'status' => 'paid',
                'paid_date' => now(),
            ]);

            return response()->json([
                'message' => 'Invoice marked as paid',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDashboard($projectId)
    {
        try {
            $budget = Budget::where('project_id', $projectId)->first();
            $expenses = Expense::where('project_id', $projectId)->get();
            $invoices = Invoice::where('project_id', $projectId)->get();

            return response()->json([
                'data' => [
                    'budget' => $budget ? [
                        'total' => $budget->amount,
                        'spent' => $budget->getSpentAmount(),
                        'remaining' => $budget->getRemainingAmount(),
                        'utilization' => $budget->getUtilization(),
                    ] : null,
                    'expenses' => [
                        'total' => $expenses->sum('amount'),
                        'pending' => $expenses->where('status', 'pending')->sum('amount'),
                        'approved' => $expenses->where('status', 'approved')->sum('amount'),
                        'by_category' => $expenses->groupBy('category')->map->sum('amount'),
                    ],
                    'invoices' => [
                        'total' => $invoices->sum('total'),
                        'paid' => $invoices->where('status', 'paid')->sum('total'),
                        'unpaid' => $invoices->where('status', '!=', 'paid')->sum('total'),
                        'overdue' => $invoices->filter->isOverdue()->sum('total'),
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
