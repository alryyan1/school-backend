<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_has_permissions')->delete();
        
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'role_id' => 1,
            ),
            1 => 
            array (
                'permission_id' => 1,
                'role_id' => 2,
            ),
            2 => 
            array (
                'permission_id' => 2,
                'role_id' => 1,
            ),
            3 => 
            array (
                'permission_id' => 2,
                'role_id' => 2,
            ),
            4 => 
            array (
                'permission_id' => 3,
                'role_id' => 1,
            ),
            5 => 
            array (
                'permission_id' => 3,
                'role_id' => 2,
            ),
            6 => 
            array (
                'permission_id' => 4,
                'role_id' => 1,
            ),
            7 => 
            array (
                'permission_id' => 4,
                'role_id' => 2,
            ),
            8 => 
            array (
                'permission_id' => 5,
                'role_id' => 1,
            ),
            9 => 
            array (
                'permission_id' => 5,
                'role_id' => 2,
            ),
            10 => 
            array (
                'permission_id' => 5,
                'role_id' => 3,
            ),
            11 => 
            array (
                'permission_id' => 6,
                'role_id' => 1,
            ),
            12 => 
            array (
                'permission_id' => 6,
                'role_id' => 2,
            ),
            13 => 
            array (
                'permission_id' => 7,
                'role_id' => 1,
            ),
            14 => 
            array (
                'permission_id' => 7,
                'role_id' => 4,
            ),
            15 => 
            array (
                'permission_id' => 8,
                'role_id' => 1,
            ),
            16 => 
            array (
                'permission_id' => 8,
                'role_id' => 2,
            ),
            17 => 
            array (
                'permission_id' => 9,
                'role_id' => 1,
            ),
            18 => 
            array (
                'permission_id' => 9,
                'role_id' => 4,
            ),
            19 => 
            array (
                'permission_id' => 10,
                'role_id' => 1,
            ),
            20 => 
            array (
                'permission_id' => 10,
                'role_id' => 2,
            ),
            21 => 
            array (
                'permission_id' => 11,
                'role_id' => 1,
            ),
            22 => 
            array (
                'permission_id' => 11,
                'role_id' => 2,
            ),
            23 => 
            array (
                'permission_id' => 11,
                'role_id' => 4,
            ),
            24 => 
            array (
                'permission_id' => 12,
                'role_id' => 1,
            ),
            25 => 
            array (
                'permission_id' => 12,
                'role_id' => 2,
            ),
            26 => 
            array (
                'permission_id' => 12,
                'role_id' => 3,
            ),
            27 => 
            array (
                'permission_id' => 12,
                'role_id' => 6,
            ),
            28 => 
            array (
                'permission_id' => 13,
                'role_id' => 1,
            ),
            29 => 
            array (
                'permission_id' => 13,
                'role_id' => 4,
            ),
            30 => 
            array (
                'permission_id' => 13,
                'role_id' => 5,
            ),
            31 => 
            array (
                'permission_id' => 14,
                'role_id' => 1,
            ),
            32 => 
            array (
                'permission_id' => 14,
                'role_id' => 2,
            ),
            33 => 
            array (
                'permission_id' => 14,
                'role_id' => 3,
            ),
            34 => 
            array (
                'permission_id' => 14,
                'role_id' => 4,
            ),
            35 => 
            array (
                'permission_id' => 15,
                'role_id' => 1,
            ),
            36 => 
            array (
                'permission_id' => 15,
                'role_id' => 2,
            ),
            37 => 
            array (
                'permission_id' => 15,
                'role_id' => 4,
            ),
            38 => 
            array (
                'permission_id' => 16,
                'role_id' => 1,
            ),
            39 => 
            array (
                'permission_id' => 16,
                'role_id' => 2,
            ),
            40 => 
            array (
                'permission_id' => 16,
                'role_id' => 4,
            ),
            41 => 
            array (
                'permission_id' => 17,
                'role_id' => 1,
            ),
            42 => 
            array (
                'permission_id' => 17,
                'role_id' => 2,
            ),
            43 => 
            array (
                'permission_id' => 17,
                'role_id' => 4,
            ),
            44 => 
            array (
                'permission_id' => 18,
                'role_id' => 1,
            ),
            45 => 
            array (
                'permission_id' => 18,
                'role_id' => 2,
            ),
            46 => 
            array (
                'permission_id' => 18,
                'role_id' => 4,
            ),
            47 => 
            array (
                'permission_id' => 19,
                'role_id' => 1,
            ),
            48 => 
            array (
                'permission_id' => 19,
                'role_id' => 2,
            ),
            49 => 
            array (
                'permission_id' => 20,
                'role_id' => 1,
            ),
            50 => 
            array (
                'permission_id' => 20,
                'role_id' => 4,
            ),
            51 => 
            array (
                'permission_id' => 21,
                'role_id' => 1,
            ),
            52 => 
            array (
                'permission_id' => 21,
                'role_id' => 2,
            ),
            53 => 
            array (
                'permission_id' => 21,
                'role_id' => 4,
            ),
            54 => 
            array (
                'permission_id' => 22,
                'role_id' => 1,
            ),
            55 => 
            array (
                'permission_id' => 22,
                'role_id' => 2,
            ),
            56 => 
            array (
                'permission_id' => 22,
                'role_id' => 4,
            ),
            57 => 
            array (
                'permission_id' => 23,
                'role_id' => 1,
            ),
            58 => 
            array (
                'permission_id' => 23,
                'role_id' => 2,
            ),
            59 => 
            array (
                'permission_id' => 23,
                'role_id' => 4,
            ),
            60 => 
            array (
                'permission_id' => 24,
                'role_id' => 1,
            ),
            61 => 
            array (
                'permission_id' => 24,
                'role_id' => 2,
            ),
            62 => 
            array (
                'permission_id' => 24,
                'role_id' => 3,
            ),
            63 => 
            array (
                'permission_id' => 24,
                'role_id' => 4,
            ),
            64 => 
            array (
                'permission_id' => 25,
                'role_id' => 1,
            ),
            65 => 
            array (
                'permission_id' => 25,
                'role_id' => 2,
            ),
            66 => 
            array (
                'permission_id' => 25,
                'role_id' => 4,
            ),
            67 => 
            array (
                'permission_id' => 26,
                'role_id' => 1,
            ),
            68 => 
            array (
                'permission_id' => 27,
                'role_id' => 1,
            ),
            69 => 
            array (
                'permission_id' => 28,
                'role_id' => 1,
            ),
            70 => 
            array (
                'permission_id' => 28,
                'role_id' => 2,
            ),
            71 => 
            array (
                'permission_id' => 28,
                'role_id' => 4,
            ),
            72 => 
            array (
                'permission_id' => 29,
                'role_id' => 1,
            ),
            73 => 
            array (
                'permission_id' => 29,
                'role_id' => 2,
            ),
            74 => 
            array (
                'permission_id' => 29,
                'role_id' => 4,
            ),
            75 => 
            array (
                'permission_id' => 30,
                'role_id' => 1,
            ),
            76 => 
            array (
                'permission_id' => 30,
                'role_id' => 2,
            ),
            77 => 
            array (
                'permission_id' => 30,
                'role_id' => 4,
            ),
            78 => 
            array (
                'permission_id' => 31,
                'role_id' => 1,
            ),
            79 => 
            array (
                'permission_id' => 31,
                'role_id' => 2,
            ),
            80 => 
            array (
                'permission_id' => 31,
                'role_id' => 4,
            ),
            81 => 
            array (
                'permission_id' => 32,
                'role_id' => 1,
            ),
            82 => 
            array (
                'permission_id' => 32,
                'role_id' => 2,
            ),
            83 => 
            array (
                'permission_id' => 32,
                'role_id' => 4,
            ),
            84 => 
            array (
                'permission_id' => 33,
                'role_id' => 1,
            ),
            85 => 
            array (
                'permission_id' => 33,
                'role_id' => 2,
            ),
            86 => 
            array (
                'permission_id' => 33,
                'role_id' => 4,
            ),
            87 => 
            array (
                'permission_id' => 34,
                'role_id' => 1,
            ),
            88 => 
            array (
                'permission_id' => 34,
                'role_id' => 2,
            ),
            89 => 
            array (
                'permission_id' => 34,
                'role_id' => 4,
            ),
            90 => 
            array (
                'permission_id' => 35,
                'role_id' => 1,
            ),
            91 => 
            array (
                'permission_id' => 35,
                'role_id' => 2,
            ),
            92 => 
            array (
                'permission_id' => 36,
                'role_id' => 1,
            ),
            93 => 
            array (
                'permission_id' => 36,
                'role_id' => 4,
            ),
            94 => 
            array (
                'permission_id' => 37,
                'role_id' => 1,
            ),
            95 => 
            array (
                'permission_id' => 37,
                'role_id' => 2,
            ),
            96 => 
            array (
                'permission_id' => 37,
                'role_id' => 3,
            ),
            97 => 
            array (
                'permission_id' => 37,
                'role_id' => 4,
            ),
            98 => 
            array (
                'permission_id' => 38,
                'role_id' => 1,
            ),
            99 => 
            array (
                'permission_id' => 38,
                'role_id' => 2,
            ),
            100 => 
            array (
                'permission_id' => 38,
                'role_id' => 3,
            ),
            101 => 
            array (
                'permission_id' => 39,
                'role_id' => 1,
            ),
            102 => 
            array (
                'permission_id' => 39,
                'role_id' => 2,
            ),
            103 => 
            array (
                'permission_id' => 39,
                'role_id' => 3,
            ),
            104 => 
            array (
                'permission_id' => 40,
                'role_id' => 1,
            ),
            105 => 
            array (
                'permission_id' => 40,
                'role_id' => 2,
            ),
            106 => 
            array (
                'permission_id' => 40,
                'role_id' => 3,
            ),
            107 => 
            array (
                'permission_id' => 41,
                'role_id' => 1,
            ),
            108 => 
            array (
                'permission_id' => 42,
                'role_id' => 1,
            ),
            109 => 
            array (
                'permission_id' => 42,
                'role_id' => 2,
            ),
            110 => 
            array (
                'permission_id' => 42,
                'role_id' => 6,
            ),
            111 => 
            array (
                'permission_id' => 43,
                'role_id' => 1,
            ),
            112 => 
            array (
                'permission_id' => 43,
                'role_id' => 2,
            ),
            113 => 
            array (
                'permission_id' => 43,
                'role_id' => 3,
            ),
            114 => 
            array (
                'permission_id' => 43,
                'role_id' => 6,
            ),
            115 => 
            array (
                'permission_id' => 44,
                'role_id' => 1,
            ),
            116 => 
            array (
                'permission_id' => 44,
                'role_id' => 2,
            ),
            117 => 
            array (
                'permission_id' => 44,
                'role_id' => 4,
            ),
            118 => 
            array (
                'permission_id' => 44,
                'role_id' => 6,
            ),
            119 => 
            array (
                'permission_id' => 45,
                'role_id' => 1,
            ),
            120 => 
            array (
                'permission_id' => 45,
                'role_id' => 2,
            ),
            121 => 
            array (
                'permission_id' => 45,
                'role_id' => 4,
            ),
            122 => 
            array (
                'permission_id' => 45,
                'role_id' => 5,
            ),
            123 => 
            array (
                'permission_id' => 46,
                'role_id' => 1,
            ),
            124 => 
            array (
                'permission_id' => 46,
                'role_id' => 2,
            ),
            125 => 
            array (
                'permission_id' => 46,
                'role_id' => 5,
            ),
            126 => 
            array (
                'permission_id' => 47,
                'role_id' => 1,
            ),
            127 => 
            array (
                'permission_id' => 47,
                'role_id' => 2,
            ),
            128 => 
            array (
                'permission_id' => 47,
                'role_id' => 4,
            ),
            129 => 
            array (
                'permission_id' => 48,
                'role_id' => 1,
            ),
            130 => 
            array (
                'permission_id' => 48,
                'role_id' => 2,
            ),
            131 => 
            array (
                'permission_id' => 48,
                'role_id' => 4,
            ),
            132 => 
            array (
                'permission_id' => 49,
                'role_id' => 1,
            ),
            133 => 
            array (
                'permission_id' => 49,
                'role_id' => 2,
            ),
            134 => 
            array (
                'permission_id' => 49,
                'role_id' => 4,
            ),
            135 => 
            array (
                'permission_id' => 50,
                'role_id' => 1,
            ),
            136 => 
            array (
                'permission_id' => 50,
                'role_id' => 2,
            ),
            137 => 
            array (
                'permission_id' => 50,
                'role_id' => 3,
            ),
            138 => 
            array (
                'permission_id' => 51,
                'role_id' => 1,
            ),
            139 => 
            array (
                'permission_id' => 51,
                'role_id' => 2,
            ),
            140 => 
            array (
                'permission_id' => 51,
                'role_id' => 4,
            ),
            141 => 
            array (
                'permission_id' => 52,
                'role_id' => 1,
            ),
            142 => 
            array (
                'permission_id' => 52,
                'role_id' => 2,
            ),
            143 => 
            array (
                'permission_id' => 52,
                'role_id' => 4,
            ),
        ));
        
        
    }
}