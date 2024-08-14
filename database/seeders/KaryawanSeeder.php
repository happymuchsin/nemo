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
            ['username' => '1284', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ranti Ohta Sari', 'join_date' => '2011/11/8'],
            ['username' => '1300', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lutfi Indrawati', 'join_date' => '2016/7/14'],
            ['username' => '2912', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Yatmi Lestari', 'join_date' => '2019/4/15'],
            ['username' => '4035', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erika Listiani', 'join_date' => '2020/11/16'],
            ['username' => '4070', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ida Masruroh', 'join_date' => '2020/12/1'],
            ['username' => '1583', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sinta Nurani', 'join_date' => '2018/3/5'],
            ['username' => '1584', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sri Rahayu', 'join_date' => '2018/3/5'],
            ['username' => '1585', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Dwi Utami', 'join_date' => '2018/3/5'],
            ['username' => '1587', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Hanifah Nur Lathifah', 'join_date' => '2018/3/5'],
            ['username' => '1643', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Pina Noviawati', 'join_date' => '2018/4/2'],
            ['username' => '1683', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Disa Andriani', 'join_date' => '2018/4/16'],
            ['username' => '4521', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Serlita Esa Virani', 'join_date' => '2021/9/20'],
            ['username' => '4446', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Alia Nurhayati', 'join_date' => '2021/8/23'],
            ['username' => '3252', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sriningsih', 'join_date' => '2019/8/5'],
            ['username' => '5138', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadya Husnandari Pratiwi', 'join_date' => '2022/7/25'],
            ['username' => '5301', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nuvita Dita Pratidina', 'join_date' => '2022/11/14'],
            ['username' => '5467', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Apriliya Juni Astuti', 'join_date' => '2023/5/15'],
        ];
        $lineOtong = [
            ['username' => '1324', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ismiyatun', 'join_date' => '2017/11/13'],
            ['username' => '3685', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Reni Restuti', 'join_date' => '2019/12/30'],
            ['username' => '3815', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Uswatun Hasanah', 'join_date' => '2020/2/24'],
            ['username' => '4378', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadhia Istiqomah', 'join_date' => '2021/7/12'],
            ['username' => '1298', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Titi Prastiwi', 'join_date' => '2017/10/18'],
            ['username' => '2007', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Yuliati', 'join_date' => '2019/7/26'],
            ['username' => '2091', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lisa Tri Veliani', 'join_date' => '2018/8/20'],
            ['username' => '4447', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Putri Maela Wati', 'join_date' => '2021/8/23'],
            ['username' => '5106', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Hasna Nurmanisa', 'join_date' => '2022/7/11'],
            ['username' => '5457', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rohmah Fajrin', 'join_date' => '2023/5/8'],
            ['username' => '5624', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Diana Putri Lestari', 'join_date' => '2023/9/11'],
            ['username' => '6211', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Zalzabilla Arie Prasetya', 'join_date' => '2024/6/3'],
            ['username' => '6418', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nabila Marjayanti', 'join_date' => '2024/7/15'],
        ];
        $lineYanti = [
            ['username' => '3589', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Naufu Luluk Fauziah', 'join_date' => '2019/11/18'],
            ['username' => '1614', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Septiani', 'join_date' => '2018/3/19'],
            ['username' => '1325', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Anita Murtiasih', 'join_date' => '2017/11/13'],
            ['username' => '1686', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wahyu Styanovanti', 'join_date' => '2018/4/16'],
            ['username' => '4353', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Mei Margiyanti', 'join_date' => '2021/7/5'],
            ['username' => '4397', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Esti Nurhayati', 'join_date' => '2021/7/26'],
            ['username' => '4352', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fitri Dhesnia', 'join_date' => '2021/7/5'],
            ['username' => '5105', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Dwi Endar Sulastri', 'join_date' => '2022/7/11'],
            ['username' => '5905', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Alya Meiliana .A', 'join_date' => '2023/12/18'],
            ['username' => '6088', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Riski Nur Azizah', 'join_date' => '2024/3/21'],
            ['username' => '6255', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lilis Septiana', 'join_date' => '2024/6/10'],
            ['username' => '6363', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Via Amanda', 'join_date' => '2024/7/1'],
            ['username' => '6382', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Afaf Rafiqah', 'join_date' => '2024/7/8'],
        ];
        $lineSunarti = [
            ['username' => '3211', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Siti Maesaroh', 'join_date' => '2019/7/29'],
            ['username' => '2518', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sulistyo Rini', 'join_date' => '2018/12/10'],
            ['username' => '3213', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Choirum Mutammimah', 'join_date' => '2019/7/29'],
            ['username' => '3816', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rizka Auliawati', 'join_date' => '2020/3/2'],
            ['username' => '4036', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erna Fatmawati', 'join_date' => '2020/11/16'],
            ['username' => '4120', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Anisa Aljanati', 'join_date' => '2020/12/28'],
            ['username' => '4386', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sariyanti', 'join_date' => '2021/7/19'],
            ['username' => '4379', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Afif Nurjani', 'join_date' => '2021/7/12'],
            ['username' => '4445', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Zuni Zuanita Anggraini', 'join_date' => '2021/8/23'],
            ['username' => '5137', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Yunita Sukamto', 'join_date' => '2022/7/25'],
            ['username' => '5621', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Esty Anggi .N', 'join_date' => '2023/9/11'],
            ['username' => '5818', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fajar Suryani', 'join_date' => '2023/11/13'],
            ['username' => '5906', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayunda Kusuma .P', 'join_date' => '2023/12/18'],
            ['username' => '6063', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Winarti', 'join_date' => '2024/3/7'],
            ['username' => '6064', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Amin Solichah', 'join_date' => '2024/3/7'],
        ];
        $lineSmsMurni = [
            ['username' => '1352', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Revviana Dita Puspita P', 'join_date' => '2019/11/20'],
            ['username' => '1354', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fitri Lestari', 'join_date' => '2017/11/20'],
            ['username' => '1387', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Siti Mukharomah', 'join_date' => '2017/12/1'],
            ['username' => '1392', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sandirah Listiyarini', 'join_date' => '2017/12/1'],
            ['username' => '1453', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Isna Wahyu Ningrum', 'join_date' => '2018/1/8'],
            ['username' => '1630', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ika Damaiyanti', 'join_date' => '2018/3/26'],
            ['username' => '1884', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Supriyati', 'join_date' => '2018/7/3'],
            ['username' => '1646', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ananda Anisya Fitri H.N', 'join_date' => '2018/4/3'],
            ['username' => '3055', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Tri Handayani', 'join_date' => '2019/6/17'],
            ['username' => '4041', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Daryanti', 'join_date' => '2020/11/16'],
            ['username' => '1457', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Sariyanti', 'join_date' => '2018/1/8'],
            ['username' => '1632', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Jezzy Widya Mukti', 'join_date' => '2018/3/26'],
            ['username' => '1633', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ria Anggesti Wahyuningsih', 'join_date' => '2018/3/26'],
            ['username' => '3855', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ria Andrianti', 'join_date' => '2020/3/16'],
            ['username' => '3856', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayu Wulandari', 'join_date' => '2020/3/16'],
            ['username' => '4713', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nadia Fitriana', 'join_date' => '2021/12/20'],
            ['username' => '5079', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Fatikha Nur Janah', 'join_date' => '2022/7/4'],
            ['username' => '5080', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Restu Wahyuningtyas', 'join_date' => '2022/7/4'],
            ['username' => '5162', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Suryaningsih', 'join_date' => '2022/8/8'],
            ['username' => '5432', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Achrunisa Pratiwi', 'join_date' => '2023/3/24'],
            ['username' => '5599', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Elva Lestari', 'join_date' => '2023/7/24'],
            ['username' => '5600', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Akhirotu Kholifah', 'join_date' => '2023/7/24'],
            ['username' => '6092', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Apriyani Saroh', 'join_date' => '2024/3/25'],
            ['username' => '6443', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Tasya Febby Kusumawati', 'join_date' => '2024/7/22'],
        ];
        $lineSmsYanti = [
            ['username' => '2202', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Aan Sutrisniati', 'join_date' => '2018/9/17'],
            ['username' => '2204', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Desy Rahmawati', 'join_date' => '2018/9/17'],
            ['username' => '2412', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ayu Pawestri', 'join_date' => '2018/11/12'],
            ['username' => '2564', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Ana Septiani Mulia', 'join_date' => '2018/12/24'],
            ['username' => '2567', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Chalimatusadiyah', 'join_date' => '2018/12/24'],
            ['username' => '2684', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nuri Wijiastuti', 'join_date' => '2019/1/28'],
            ['username' => '1615', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Desty Murningsih', 'join_date' => '2018/3/19'],
            ['username' => '2092', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Chanifatus Solichah', 'join_date' => '2018/8/20'],
            ['username' => '2089', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Viky Nur Afiriastuti', 'join_date' => '2018/8/20'],
            ['username' => '2095', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rahma Wulan Sari', 'join_date' => '2018/8/20'],
            ['username' => '2205', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Diah Heni Untari', 'join_date' => '2018/9/17'],
            ['username' => '3665', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rossa Wiraviany', 'join_date' => '2019/12/17'],
            ['username' => '4548', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wulan Prihatini', 'join_date' => '2021/10/4'],
            ['username' => '2201', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Rini Wulandari', 'join_date' => '2018/9/17'],
            ['username' => '4772', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Wayang Asty Yunisa', 'join_date' => '2022/1/10'],
            ['username' => '5139', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Erna Anggraeni', 'join_date' => '2022/7/25'],
            ['username' => '5302', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Lusia Sri Dwi Astuti', 'join_date' => '2022/11/17'],
            ['username' => '5782', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Vivin Wahyuningtyas', 'join_date' => '2023/10/30'],
            ['username' => '5622', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Nurul Fidia', 'join_date' => '2023/9/11'],
            ['username' => '5888', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Findysa Keysa .A', 'join_date' => '2023/12/11'],
            ['username' => '6229', 'master_division_id' => 2, 'master_position_id' => 2, 'name' => 'Kriswijiyanti', 'join_date' => '2024/6/6'],
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
                'join_date' => date('Y-m-d', strtotime($l['join_date'])),
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
