<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $listDataCurrencies = [
            [
                'code' => 'IDR',
                'name' => 'Indonesian Rupiah',
            ],
            [
                'code' => 'USD',
                'name' => 'United States Dollar',
            ]
        ];

        // insert into insurance_type if not exists
        foreach ($listDataCurrencies as $data) {
            \App\Models\Currency::firstOrCreate([
                'code' => $data['code'],
                'name' => $data['name'],
            ]);
        }

        $listData = [
            'Property',
            'Automobile',
            'Marine Cargo',
            'Personal Accident',
            'Customs Bond',
        ];

        // insert into insurance_type if not exists
        foreach ($listData as $data) {
            \App\Models\ContractType::firstOrCreate([
                'name' => $data,
            ]);
        }

        $listAccountCategories = [
            'Akun Piutang',
            'Aktiva Lancar Lainnya',
            'Kas Bank',
            'Persediaan',
            'Aktiva Tetap',
            'Aktiva Lainnya',
            'Depresiasi dan Amortisasi',
            'Akun Hutang',
            'Kartu Kredit',
            'Kewajiban Lancar Lainnya',
            'Kewajiban Jangka Panjang',
            'Ekuitas',
            'Pendapatan',
            'Pendapatan Lainnya',
            'Harga Pokok Penjualan',
            'Beban',
            'Beban Lainnya',
        ];

        foreach ($listAccountCategories as $r) {
            \App\Models\AccountCategory::firstOrCreate([
                'name' => $r,
            ]);
        }
    }
}
