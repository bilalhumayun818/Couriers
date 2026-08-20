<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('trips')
            ->withSum(['trips as total_invoiced' => function ($q) {
                $q->where('status', 'active');
            }], 'total_amount');

        if ($request->filled('search')) {
            $query->where('company_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('balance')) {
            if ($request->balance === 'with') {
                $query->having('total_invoiced', '>', 0);
            } elseif ($request->balance === 'none') {
                $query->having('total_invoiced', '=', 0)->orWhereNull('total_invoiced');
            }
        }

        $customers = $query->orderBy('company_name')->paginate(15)->withQueryString();

        return view('demo.crm.customers', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:30',
            'billing_address'=> 'nullable|string|max:1000',
            'credit_limit'   => 'nullable|numeric|min:0|max:999999999.99',
        ]);

        Customer::create($data);

        return redirect()->route('crm.customers')
            ->with('success', 'Customer "' . $data['company_name'] . '" added successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'trips' => fn($q) => $q->with('van')->orderByDesc('trip_date')->take(20),
        ]);

        $totalInvoiced  = $customer->trips->where('status', 'active')->sum('total_amount');
        $totalTrips     = $customer->trips->where('status', 'active')->count();

        return view('demo.crm.customer-detail', compact(
            'customer', 'totalInvoiced', 'totalTrips'
        ));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:30',
            'billing_address'=> 'nullable|string|max:1000',
            'credit_limit'   => 'nullable|numeric|min:0|max:999999999.99',
        ]);

        $customer->update($data);

        return redirect()->route('crm.customers')
            ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('crm.customers')
            ->with('success', '"' . $customer->company_name . '" removed.');
    }
}
