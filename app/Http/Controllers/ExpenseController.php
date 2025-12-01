<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index()
    {
        
        $expenses = Expense::latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'category' => $request->category,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'note' => $request->note,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense Added Successfully!');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense Deleted!');
    }
}
