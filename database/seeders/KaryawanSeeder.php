<?php

namespace Database\Seeders;

use App\Models\MasterLine;
use App\Models\MasterPlacement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lineAceng = [
            ['username' => '1284', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ranti Ohta Sari'],
            ['username' => '1300', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lutfi Indrawati'],
            ['username' => '2912', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Yatmi Lestari'],
            ['username' => '4035', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erika Listiani'],
            ['username' => '4070', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ida Masruroh'],
            ['username' => '1583', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sinta Nurani'],
            ['username' => '1584', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sri Rahayu'],
            ['username' => '1585', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Dwi Utami'],
            ['username' => '1587', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Hanifah Nur Lathifah'],
            ['username' => '1643', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Pina Noviawati'],
            ['username' => '1683', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Disa Andriani'],
            ['username' => '4521', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Serlita Esa Virani'],
            ['username' => '4446', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Alia Nurhayati'],
            ['username' => '3252', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sriningsih'],
            ['username' => '5138', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadya Husnandari Pratiwi'],
            ['username' => '5301', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nuvita Dita Pratidina'],
            ['username' => '5467', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Apriliya Juni Astuti']
        ];
        $lineOtong = [
            ['username' => '1324', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ismiyatun'],
            ['username' => '3685', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Reni Restuti'],
            ['username' => '3815', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Uswatun Hasanah'],
            ['username' => '4378', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadhia Istiqomah'],
            ['username' => '1298', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Titi Prastiwi'],
            ['username' => '2007', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Yuliati'],
            ['username' => '2091', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lisa Tri Veliani'],
            ['username' => '4447', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Putri Maela Wati'],
            ['username' => '5106', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Hasna Nurmanisa'],
            ['username' => '5457', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rohmah Fajrin'],
            ['username' => '5624', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Diana Putri Lestari'],
            ['username' => '6211', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Zalzabilla Arie Prasetya'],
            ['username' => '6418', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nabila Marjayanti']
        ];
        $lineYanti = [
            ['username' => '3589', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Naufu Luluk Fauziah'],
            ['username' => '1614', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Septiani'],
            ['username' => '1325', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Anita Murtiasih'],
            ['username' => '1686', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wahyu Styanovanti'],
            ['username' => '4353', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Mei Margiyanti'],
            ['username' => '4397', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Esti Nurhayati'],
            ['username' => '4352', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fitri Dhesnia'],
            ['username' => '5105', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Dwi Endar Sulastri'],
            ['username' => '5905', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Alya Meiliana .A'],
            ['username' => '6088', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Riski Nur Azizah'],
            ['username' => '6255', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lilis Septiana'],
            ['username' => '6363', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Via Amanda'],
            ['username' => '6382', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Afaf Rafiqah'],
        ];
        $lineSunarti = [
            ['username' => '3211', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Siti Maesaroh'],
            ['username' => '2518', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sulistyo Rini'],
            ['username' => '3213', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Choirum Mutammimah'],
            ['username' => '3816', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rizka Auliawati'],
            ['username' => '4036', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erna Fatmawati'],
            ['username' => '4120', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Anisa Aljanati'],
            ['username' => '4386', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sariyanti'],
            ['username' => '4379', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Afif Nurjani'],
            ['username' => '4445', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Zuni Zuanita Anggraini'],
            ['username' => '5137', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Yunita Sukamto'],
            ['username' => '5621', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Esty Anggi .N'],
            ['username' => '5818', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fajar Suryani'],
            ['username' => '5906', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayunda Kusuma .P'],
            ['username' => '6063', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Winarti'],
            ['username' => '6064', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Amin Solichah']
        ];
        $lineSmsMurni = [
            ['username' => '1352', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Revviana Dita Puspita P'],
            ['username' => '1354', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fitri Lestari'],
            ['username' => '1387', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Siti Mukharomah'],
            ['username' => '1392', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sandirah Listiyarini'],
            ['username' => '1453', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Isna Wahyu Ningrum'],
            ['username' => '1630', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ika Damaiyanti'],
            ['username' => '1884', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Supriyati'],
            ['username' => '1646', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ananda Anisya Fitri H.N'],
            ['username' => '3055', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Tri Handayani'],
            ['username' => '4041', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Daryanti'],
            ['username' => '1457', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sariyanti'],
            ['username' => '1632', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Jezzy Widya Mukti'],
            ['username' => '1633', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ria Anggesti Wahyuningsih'],
            ['username' => '3855', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ria Andrianti'],
            ['username' => '3856', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayu Wulandari'],
            ['username' => '4713', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadia Fitriana'],
            ['username' => '5079', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fatikha Nur Janah'],
            ['username' => '5080', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Restu Wahyuningtyas'],
            ['username' => '5162', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Suryaningsih'],
            ['username' => '5432', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Achrunisa Pratiwi'],
            ['username' => '5599', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Elva Lestari'],
            ['username' => '5600', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Akhirotu Kholifah'],
            ['username' => '6092', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Apriyani Saroh'],
            ['username' => '6443', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Tasya Febby Kusumawati']
        ];
        $lineSmsYanti = [
            ['username' => '2202', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Aan Sutrisniati'],
            ['username' => '2204', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Desy Rahmawati'],
            ['username' => '2412', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayu Pawestri'],
            ['username' => '2564', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ana Septiani Mulia'],
            ['username' => '2567', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Chalimatusadiyah'],
            ['username' => '2684', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nuri Wijiastuti'],
            ['username' => '1615', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Desty Murningsih'],
            ['username' => '2092', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Chanifatus Solichah'],
            ['username' => '2089', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Viky Nur Afiriastuti'],
            ['username' => '2095', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rahma Wulan Sari'],
            ['username' => '2205', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Diah Heni Untari'],
            ['username' => '3665', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rossa Wiraviany'],
            ['username' => '4548', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wulan Prihatini'],
            ['username' => '2201', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Wulandari'],
            ['username' => '4772', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wayang Asty Yunisa'],
            ['username' => '5139', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erna Anggraeni'],
            ['username' => '5302', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lusia Sri Dwi Astuti'],
            ['username' => '5782', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Vivin Wahyuningtyas'],
            ['username' => '5622', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nurul Fidia'],
            ['username' => '5888', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Findysa Keysa .A'],
            ['username' => '6229', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Kriswijiyanti']
        ];
        $reff = 'line';
        $aceng = MasterLine::where('name', 'LINE ACENG')->first()->id;
        $otong = MasterLine::where('name', 'LINE OTONG')->first()->id;
        $yanti = MasterLine::where('name', 'LINE YANTI')->first()->id;
        $sunarti = MasterLine::where('name', 'LINE SUNARTI')->first()->id;
        $smsMurni = MasterLine::where('name', 'LINE SMS MURNI')->first()->id;
        $smsYanti = MasterLine::where('name', 'LINE SMS YANTI')->first()->id;

        foreach ($lineAceng as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $aceng,
                'created_by' => 'developer',
            ]);
        }

        foreach ($lineOtong as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $otong,
                'created_by' => 'developer',
            ]);
        }

        foreach ($lineYanti as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $yanti,
                'created_by' => 'developer',
            ]);
        }

        foreach ($lineSunarti as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $sunarti,
                'created_by' => 'developer',
            ]);
        }

        foreach ($lineSmsMurni as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $smsMurni,
                'created_by' => 'developer',
            ]);
        }

        foreach ($lineSmsYanti as $l) {
            $i = User::firstOrCreate([
                'username' => $l['username'],
                'name' => trim(strtoupper($l['name'])),
                'master_division_id' => $l['master_division_id'],
                'master_position_id' => $l['master_position_id'],
                'password' => bcrypt($l['username']),
                'created_by' => 'developer',
            ]);
            MasterPlacement::firstOrCreate([
                'user_id' => $i->id,
                'reff' => $reff,
                'location_id' => $smsYanti,
                'created_by' => 'developer',
            ]);
        }
    }
}
