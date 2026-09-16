<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()->role === 'Admin', 403);
        return view('settings.tenant', ['settings' => TenantSetting::current()]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->role === 'Admin', 403);
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'currency_code' => ['required', Rule::in(['USD', 'KES', 'GBP', 'EUR', 'ZAR'])],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'tax_rate' => ['required', 'numeric', 'between:0,100', 'decimal:0,2'],
            'tax_label' => ['required', 'string', 'max:40'],
        ]);
        TenantSetting::updateOrCreate(['id' => 1], $data);
        return redirect()->route('settings.tenant')->with('success', 'Settings saved successfully.');
    }
}
