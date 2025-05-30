<?php

namespace Database\Seeders;

use App\Models\MasterNeedle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterNeedleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/R", 'brand' => "GB", 'size' => "8", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "12", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "13", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "14", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/R", 'brand' => "GB", 'size' => "16", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1", 'code' => "BOLPOINT/R", 'brand' => "GB", 'size' => "18", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "10", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "12", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "13", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "14", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "16", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "18", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "18", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "21", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/R", 'brand' => "GB", 'size' => "9", 'machine' => "button fix, ams"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "button fix, ams"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "button fix, ams"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "12", 'machine' => "button fix, ams"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/R", 'brand' => "GB", 'size' => "13", 'machine' => "button fix, ams"],
            ['tipe' => "DP X 17", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "14", 'machine' => "button fix, ams"],
            ['tipe' => "DC X 27", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "overlock"],
            ['tipe' => "DC X 27", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "overlock"],
            ['tipe' => "DC X 27", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "13", 'machine' => "overlock"],
            ['tipe' => "DC X 27", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "14", 'machine' => "overlock"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "9", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "12", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "13", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "14", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "16", 'machine' => "Kansai"],
            ['tipe' => "UO X 113", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "18", 'machine' => "Kansai"],
            ['tipe' => "TV X 1", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "12", 'machine' => "Chainstitch, foa"],
            ['tipe' => "TV X 1", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "14", 'machine' => "Chainstitch, foa"],
            ['tipe' => "TV X 1", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "16", 'machine' => "Chainstitch, foa"],
            ['tipe' => "TV X 1", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "18", 'machine' => "Chainstitch, foa"],
            ['tipe' => "TV X 5", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "11", 'machine' => "Foa"],
            ['tipe' => "TV X 5", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "12", 'machine' => "Foa"],
            ['tipe' => "TV X 5", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "14", 'machine' => "Foa"],
            ['tipe' => "TV X 5", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "16", 'machine' => "Foa"],
            ['tipe' => "TV X 5", 'code' => "BOLPOINT/RG", 'brand' => "GB", 'size' => "18", 'machine' => "Foa"],
            ['tipe' => "DO X 558", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "12", 'machine' => "Keyhole"],
            ['tipe' => "DO X 558", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "14", 'machine' => "Keyhole"],
            ['tipe' => "DO X 558", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "16", 'machine' => "Keyhole"],
            ['tipe' => "DO X 558", 'code' => "BOLPOINT/RS/SPI", 'brand' => "GB", 'size' => "18", 'machine' => "Keyhole"],
            ['tipe' => "DB X 1 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "7", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DP X 5 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "7", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DP X 5 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "Double Needle, single needle, bartack, button hole"],
            ['tipe' => "DC X 27 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "overlock"],
            ['tipe' => "DC X 27 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "9", 'machine' => "overlock"],
            ['tipe' => "DC X 27 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "overlock"],
            ['tipe' => "DC X 27 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "11", 'machine' => "overlock"],
            ['tipe' => "DC X 27 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "8", 'machine' => "overlock"],
            ['tipe' => "DC X 27 SAN 10 XS", 'code' => "SANTEN/FFG/SES", 'brand' => "GB", 'size' => "10", 'machine' => "overlock"],
            ['tipe' => "DB X 1 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DP X 5 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 SERV 7", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DC X 27", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "10", 'machine' => "overlock"],
            ['tipe' => "DC X 27", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "overlock"],
            ['tipe' => "DB X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "9", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DC X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Overlock"],
            ['tipe' => "DC X 1 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Overlock"],
            ['tipe' => "DP X 5 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "9", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 KN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DB X 1 TN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 TN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "12", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DP X 5 TN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 TN", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DO X 558", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "12", 'machine' => "keyhole"],
            ['tipe' => "DO X 558", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "keyhole"],
            ['tipe' => "DO X 558", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "keyhole"],
            ['tipe' => "DV X 57 SUK", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "14", 'machine' => "kansai"],
            ['tipe' => "DV X 57 SUK", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "16", 'machine' => "kansai"],
            ['tipe' => "CP X 12", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "18", 'machine' => "sadlle stitch"],
            ['tipe' => "CP X 12", 'code' => "SCHMETZ SERV 7/SCHMETZ", 'brand' => "SCHMETZ", 'size' => "21", 'machine' => "sadlle stitch"],
            ['tipe' => "DB X 1 GEBEDUR", 'code' => "GEBEDUR/R", 'brand' => "GROSZ BECKERT", 'size' => "9", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 GEBEDUR", 'code' => "GEBEDUR/R", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DB X 1 GEBEDUR", 'code' => "GEBEDUR/R", 'brand' => "GROSZ BECKERT", 'size' => "14", 'machine' => "Single needle, single needle cutter, cnc"],
            ['tipe' => "DP X 5 GEBEDUR", 'code' => "GEBEDUR/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "9", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 GEBEDUR", 'code' => "GEBEDUR/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "10", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 GEBEDUR", 'code' => "GEBEDUR/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 GEBEDUR", 'code' => "GEBEDUR/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "14", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "DP X 5 GEBEDUR", 'code' => "GEBEDUR/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "16", 'machine' => "Single needle, double needle, bartack, button hole"],
            ['tipe' => "UY X 128", 'code' => "GAS/RG", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "overdeck"],
            ['tipe' => "UY X 128", 'code' => "GAS/RG", 'brand' => "GROSZ BECKERT", 'size' => "13", 'machine' => "overdeck"],
            ['tipe' => "UY X 128 #GBS", 'code' => "GAS/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "14", 'machine' => "overdeck"],
            ['tipe' => "UY X 128 MR #14", 'code' => "GAS/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "14", 'machine' => "overdeck"],
            ['tipe' => "UY X 113 GHS", 'code' => "GAS", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "overdeck"],
            ['tipe' => "UY 180 GVS", 'code' => "GAS/R", 'brand' => "GROSZ BECKERT", 'size' => "16", 'machine' => "overdeck"],
            ['tipe' => "29 BL", 'code' => "GAS/RS EM", 'brand' => "GROSZ BECKERT", 'size' => "9", 'machine' => "SUM"],
            ['tipe' => "29 BL", 'code' => "GAS/RS EM", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "SUM"],
            ['tipe' => "UY X 118 GKS", 'code' => "GAS/RG", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "OVERDECK"],
            ['tipe' => "UY X 118 GKS", 'code' => "GAS/RG", 'brand' => "GROSZ BECKERT", 'size' => "12", 'machine' => "OVERDECK"],
            ['tipe' => "UY X 118 GKS", 'code' => "GAS/RG", 'brand' => "GROSZ BECKERT", 'size' => "14", 'machine' => "OVERDECK"],
            ['tipe' => "UY X 118 SAN 10", 'code' => "SANTEN/FFG/SES", 'brand' => "GROSZ BECKERT", 'size' => "11", 'machine' => "OVERDECK"],
            ['tipe' => "DB X K5", 'code' => "GROZ BECKERT/RG", 'brand' => "GROSZ BECKERT", 'size' => "8", 'machine' => "EMBRO"],
            ['tipe' => "DB X K5", 'code' => "GROZ BECKERT/RG", 'brand' => "GROSZ BECKERT", 'size' => "9", 'machine' => "EMBRO"],
        ];

        foreach ($data as $key => $data) {
            MasterNeedle::firstOrCreate([
                'id' => $key + 1,
                'brand' => trim(strtoupper($data['brand'])),
                'tipe' => trim(strtoupper($data['tipe'])),
                'size' => trim(strtoupper($data['size'])),
                'code' => trim(strtoupper($data['code'])),
                'machine' => trim(strtoupper($data['machine'])),
                'created_by' => 'developer',
            ]);
        }
    }
}
