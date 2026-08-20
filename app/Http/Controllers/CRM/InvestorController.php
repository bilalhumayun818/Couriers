<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorTransaction;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::with('transactions')->orderBy('full_name')->get();

        $totals = [
            'capital'       => $investors->sum('initial_capital'),
            'injections'    => $investors->sum(fn($i) => $i->total_injections),
            'distributions' => $investors->sum(fn($i) => $i->total_distributions),
            'retained'      => $investors->sum(fn($i) => $i->retained_balance),
        ];

        return view('demo.crm.investors', compact('investors', 'totals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'role'                => 'required|in:investor,director',
            'bank_account_name'   => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name'           => 'nullable|string|max:255',
            'initial_capital'     => 'nullable|numeric|min:0',
        ]);
        $data['initial_capital'] = $data['initial_capital'] ?? 0;
        Investor::create($data);
        return redirect()->route('crm.investors')->with('success', '"' . $data['full_name'] . '" added.');
    }

    public function update(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'role'                => 'required|in:investor,director',
            'bank_account_name'   => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name'           => 'nullable|string|max:255',
            'initial_capital'     => 'nullable|numeric|min:0',
        ]);
        $data['initial_capital'] = $data['initial_capital'] ?? 0;
        $investor->update($data);
        return redirect()->route('crm.investors')->with('success', 'Investor updated.');
    }

    public function destroy(Investor $investor)
    {
        $investor->delete();
        return redirect()->route('crm.investors')->with('success', '"' . $investor->full_name . '" removed.');
    }

    public function inject(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|string|max:500',
        ]);
        InvestorTransaction::create(array_merge($data, [
            'investor_id' => $investor->id,
            'type'        => 'injection',
        ]));
        return redirect()->route('crm.investors')->with('success', 'Capital injection recorded for ' . $investor->full_name . '.');
    }

    public function distribute(Request $request, Investor $investor)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|string|max:500',
        ]);

        $balance = $investor->load('transactions')->retained_balance;
        if ((float)$data['amount'] > $balance) {
            return back()->withErrors(['amount' => 'Distribution amount exceeds retained balance of $' . number_format($balance, 2)])->withInput();
        }

        InvestorTransaction::create(array_merge($data, [
            'investor_id' => $investor->id,
            'type'        => 'distribution',
        ]));
        return redirect()->route('crm.investors')->with('success', 'Distribution recorded for ' . $investor->full_name . '.');
    }
}
