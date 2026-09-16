<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Investor;
use App\Models\InvestorTransaction;
use Carbon\Carbon;

class InvestorSeeder extends Seeder
{
    public function run(): void
    {
        $investors = [
            [
                'full_name'          => 'Robert Kamau',
                'role'               => 'director',
                'bank_account_name'  => 'Robert K. Kamau',
                'bank_account_number'=> 'KE1234567890001',
                'bank_name'          => 'Equity Bank',
                'initial_capital'    => 50000.00,
            ],
            [
                'full_name'          => 'Susan Otieno',
                'role'               => 'investor',
                'bank_account_name'  => 'Susan A. Otieno',
                'bank_account_number'=> 'KE0987654321002',
                'bank_name'          => 'KCB Bank',
                'initial_capital'    => 30000.00,
            ],
            [
                'full_name'          => 'Michael Njoroge',
                'role'               => 'director',
                'bank_account_name'  => 'Michael N. Njoroge',
                'bank_account_number'=> 'KE1122334455003',
                'bank_name'          => 'Equity Bank',
                'initial_capital'    => 40000.00,
            ],
            [
                'full_name'          => 'Patricia Waweru',
                'role'               => 'investor',
                'bank_account_name'  => 'Patricia W. Waweru',
                'bank_account_number'=> 'KE5544332211004',
                'bank_name'          => 'Co-op Bank',
                'initial_capital'    => 20000.00,
            ],
        ];

        foreach ($investors as $data) {
            $inv = Investor::whereNull('demo_token')->firstOrCreate(
                ['full_name' => $data['full_name']],
                $data
            );

            // Seed transactions
            $txns = [
                ['injection',    12, 5000.00,  'Additional capital injection'],
                ['injection',    6,  8000.00,  'Q2 capital top-up'],
                ['distribution', 4,  2000.00,  'Q1 profit distribution'],
                ['distribution', 1,  3000.00,  'Q2 profit distribution'],
            ];

            foreach ($txns as [$type, $monthsAgo, $amount, $desc]) {
                $date = Carbon::now()->subMonthsNoOverflow($monthsAgo)->startOfMonth()->toDateString();
                InvestorTransaction::whereNull('demo_token')->whereDate('transaction_date', $date)->firstOrCreate(
                    [
                        'investor_id'      => $inv->id,
                        'type'             => $type,
                        'amount'           => $amount * ($inv->initial_capital / 50000), // scale by capital
                    ],
                    ['description' => $desc, 'transaction_date' => $date]
                );
            }
        }

        $this->command->info('✓ Investors seeded: ' . count($investors) . ' investors with transactions.');
    }
}
