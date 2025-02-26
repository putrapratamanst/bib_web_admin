<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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

        $listContractTypis = [
            'Property',
            'Automobile',
        ];

        foreach ($listContractTypis as $r) {
            \App\Models\ContractType::firstOrCreate([
                'name' => $r,
            ]);
        }

        $listCurrencies = [
            'IDR',
            'USD'
        ];

        foreach ($listCurrencies as $r) {
            \App\Models\Currency::firstOrCreate([
                'code' => $r,
                'name' => $r,
            ]);
        }
    }
}
