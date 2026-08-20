<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Van;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('van')
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('van_id')) {
            $query->where('van_id', $request->van_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->where('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('expense_date', '<=', $request->to);
        }

        $expenses   = $query->paginate(15)->withQueryString();
        $vans       = Van::orderBy('plate_number')->get();
        $categories = Expense::$categories;

        // Summary totals for current filter (unpaginated)
        $totalsQuery = Expense::where('status', 'active');
        if ($request->filled('van_id'))   $totalsQuery->where('van_id', $request->van_id);
        if ($request->filled('category')) $totalsQuery->where('category', $request->category);
        if ($request->filled('from'))     $totalsQuery->where('expense_date', '>=', $request->from);
        if ($request->filled('to'))       $totalsQuery->where('expense_date', '<=', $request->to);

        $totalAmount = $totalsQuery->sum('amount');
        $totalCount  = $totalsQuery->count();

        return view('demo.operations.expenses', compact(
            'expenses', 'vans', 'categories', 'totalAmount', 'totalCount'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'van_id'       => 'required|exists:vans,id',
            'category'     => 'required|in:' . implode(',', Expense::$categories),
            'expense_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01|max:999999.99',
            'description'  => 'nullable|string|max:500',
        ]);

        Expense::create(array_merge($data, ['status' => 'active']));

        return redirect()->route('operations.expenses')
            ->with('success', 'Expense logged successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'van_id'       => 'required|exists:vans,id',
            'category'     => 'required|in:' . implode(',', Expense::$categories),
            'expense_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01|max:999999.99',
            'description'  => 'nullable|string|max:500',
        ]);

        $expense->update($data);

        return redirect()->route('operations.expenses')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->update(['status' => 'deleted']);

        return redirect()->route('operations.expenses')
            ->with('success', 'Expense #' . str_pad($expense->id, 5, '0', STR_PAD_LEFT) . ' deleted.');
    }
}
