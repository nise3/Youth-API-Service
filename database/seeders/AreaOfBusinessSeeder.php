<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AreaOfBusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('area_of_business')->truncate();

        $areaOfBusiness = array(

            array(
                'id' => 13,
                'title' => 'Advertising Agency',
            ),

            array(
                'id' => 48,
                'title' => 'Agro based firms (incl. Agro Processing/Seed/GM)',
            ),

            array(
                'id' => 43,
                'title' => 'Airline',
            ),

            array(
                'id' => 2090,
                'title' => 'Amusement Park',
            ),

            array(
                'id' => 1100,
                'title' => 'Animal/Plant Breeding',
            ),

            array(
                'id' => 52,
                'title' => 'Architecture Firm',
            ),

            array(
                'id' => 38,
                'title' => 'Audit Firms /Tax Consultant',
            ),

            array(
                'id' => 70,
                'title' => 'Automobile',
            ),

            array(
                'id' => 2107,
                'title' => 'Bakery (Cake, Biscuit, Bread)',
            ),

            array(
                'id' => 1,
                'title' => 'Banks',
            ),

            array(
                'id' => 97,
                'title' => 'Bar/Pub',
            ),

            array(
                'id' => 111,
                'title' => 'Battery, Storage cell',
            ),

            array(
                'id' => 2126,
                'title' => 'Beauty Parlor/Saloon/Spa',
            ),

            array(
                'id' => 2105,
                'title' => 'Beverage',
            ),

            array(
                'id' => 116,
                'title' => 'Bicycle',
            ),

            array(
                'id' => 119,
                'title' => 'Boutique/ Fashion',
            ),

            array(
                'id' => 18,
                'title' => 'BPO/ Data Entry Firm',
            ),

            array(
                'id' => 87,
                'title' => 'Brick',
            ),

            array(
                'id' => 37,
                'title' => 'Buying House',
            ),

            array(
                'id' => 68,
                'title' => 'Call Center',
            ),

            array(
                'id' => 1120,
                'title' => 'Call Center',
            ),

            array(
                'id' => 95,
                'title' => 'Catering',
            ),

            array(
                'id' => 1121,
                'title' => 'Cellular Phone Operator',
            ),

            array(
                'id' => 106,
                'title' => 'Cement',
            ),

            array(
                'id' => 75,
                'title' => 'Cement Industry',
            ),

            array(
                'id' => 1106,
                'title' => 'Chain shop',
            ),

            array(
                'id' => 2124,
                'title' => 'Chamber',
            ),

            array(
                'id' => 67,
                'title' => 'Chemical Industries',
            ),

            array(
                'id' => 2091,
                'title' => 'Cinema Hall/Theater',
            ),

            array(
                'id' => 27,
                'title' => 'Clearing & Forwarding (C&F) Companies',
            ),
            array(
                'id' => 91,
                'title' => 'Clinic',
            ),
            array(
                'id' => 2098,
                'title' => 'Club',
            ),
            array(
                'id' => 49,
                'title' => 'CNG',
            ),
            array(
                'id' => 2120,
                'title' => 'CNG Conversion',
            ),
            array(
                'id' => 1136,
                'title' => 'Coaching Center',
            ),
            array(
                'id' => 1129,
                'title' => 'Coal',
            ),
            array(
                'id' => 94,
                'title' => 'Coffee Shop',
            ),
            array(
                'id' => 11,
                'title' => 'College',
            ),
            array(
                'id' => 20,
                'title' => 'Computer Hardware/Network Companies',
            ),
            array(
                'id' => 34,
                'title' => 'Consulting Firms',
            ),
            array(
                'id' => 2093,
                'title' => 'Convention center',
            ),
            41 =>
                array(
                    'id' => 2115,
                    'title' => 'Corrugated Tin',
                ),
            42 =>
                array(
                    'id' => 59,
                    'title' => 'Cosmetics/Toiletries/Personal Care',
                ),
            43 =>
                array(
                    'id' => 1090,
                    'title' => 'Credit Rating Agency',
                ),
            44 =>
                array(
                    'id' => 121,
                    'title' => 'Crockeries',
                ),
            45 =>
                array(
                    'id' => 2092,
                    'title' => 'Cultural Centre',
                ),
            46 =>
                array(
                    'id' => 2113,
                    'title' => 'Dairy',
                ),
            47 =>
                array(
                    'id' => 1108,
                    'title' => 'Departmental store',
                ),
            48 =>
                array(
                    'id' => 14,
                    'title' => 'Design/Printing/Publishing',
                ),
            49 =>
                array(
                    'id' => 1105,
                    'title' => 'Developer',
                ),
            50 =>
                array(
                    'id' => 23,
                    'title' => 'Development Agency',
                ),
            51 =>
                array(
                    'id' => 90,
                    'title' => 'Diagnostic Centre',
                ),
            52 =>
                array(
                    'id' => 54,
                    'title' => 'Direct Selling/Marketing Service Company',
                ),
            53 =>
                array(
                    'id' => 114,
                    'title' => 'Dry cell (Battery)',
                ),
            54 =>
                array(
                    'id' => 1119,
                    'title' => 'DTP House',
                ),
            55 =>
                array(
                    'id' => 1094,
                    'title' => 'Dyeing Factory',
                ),
            56 =>
                array(
                    'id' => 2117,
                    'title' => 'E-commerce',
                ),
            57 =>
                array(
                    'id' => 110,
                    'title' => 'Electric Wire/Cable',
                ),
            58 =>
                array(
                    'id' => 61,
                    'title' => 'Electronic Equipment/Home Appliances',
                ),
            59 =>
                array(
                    'id' => 31,
                    'title' => 'Embassies/Foreign Consulate',
                ),
            60 =>
                array(
                    'id' => 32,
                    'title' => 'Engineering Firms',
                ),
            61 =>
                array(
                    'id' => 1103,
                    'title' => 'Escalator/Elevator/Lift',
                ),
            62 =>
                array(
                    'id' => 15,
                    'title' => 'Event Management',
                ),
            63 =>
                array(
                    'id' => 1098,
                    'title' => 'Farming',
                ),
            64 =>
                array(
                    'id' => 96,
                    'title' => 'Fast Food Shop',
                ),
            65 =>
                array(
                    'id' => 1130,
                    'title' => 'Filling Station',
                ),
            66 =>
                array(
                    'id' => 1117,
                    'title' => 'Film Production',
                ),
            67 =>
                array(
                    'id' => 1091,
                    'title' => 'Financial Consultants',
                ),
            68 =>
                array(
                    'id' => 2109,
                    'title' => 'Fire Fighting and Safety',
                ),
            69 =>
                array(
                    'id' => 81,
                    'title' => 'Fisheries',
                ),
            70 =>
                array(
                    'id' => 2104,
                    'title' => 'Food (Packaged)',
                ),
            71 =>
                array(
                    'id' => 47,
                    'title' => 'Food (Packaged)/Beverage',
                ),
            72 =>
                array(
                    'id' => 64,
                    'title' => 'Freight forwarding',
                ),
            73 =>
                array(
                    'id' => 2123,
                    'title' => 'Fuel/Petroleum',
                ),
            74 =>
                array(
                    'id' => 115,
                    'title' => 'Furniture',
                ),
            75 =>
                array(
                    'id' => 76,
                    'title' => 'Furniture Manufacturer',
                ),
            76 =>
                array(
                    'id' => 2097,
                    'title' => 'Gallery',
                ),
            77 =>
                array(
                    'id' => 35,
                    'title' => 'Garments',
                ),
            78 =>
                array(
                    'id' => 78,
                    'title' => 'Garments Accessories',
                ),
            79 =>
                array(
                    'id' => 1128,
                    'title' => 'Gas',
                ),
            80 =>
                array(
                    'id' => 2099,
                    'title' => 'Golf Club',
                ),
            81 =>
                array(
                    'id' => 21,
                    'title' => 'Govt./ Semi Govt./ Autonomous body',
                ),
            82 =>
                array(
                    'id' => 1112,
                    'title' => 'Grocery shop',
                ),
            83 =>
                array(
                    'id' => 66,
                    'title' => 'Group of Companies',
                ),
            84 =>
                array(
                    'id' => 98,
                    'title' => 'GSA',
                ),
            85 =>
                array(
                    'id' => 117,
                    'title' => 'Handicraft',
                ),
            86 =>
                array(
                    'id' => 2111,
                    'title' => 'Hatchery',
                ),
            87 =>
                array(
                    'id' => 2114,
                    'title' => 'Healthcare/Lifestyle product',
                ),
            88 =>
                array(
                    'id' => 82,
                    'title' => 'Herbal Medicine',
                ),
            89 =>
                array(
                    'id' => 39,
                    'title' => 'Hospital',
                ),
            90 =>
                array(
                    'id' => 41,
                    'title' => 'Hotel',
                ),
            91 =>
                array(
                    'id' => 1104,
                    'title' => 'HVAC System',
                ),
            92 =>
                array(
                    'id' => 2106,
                    'title' => 'Ice Cream',
                ),
            93 =>
                array(
                    'id' => 51,
                    'title' => 'Immigration & Education Consultancy Service',
                ),
            94 =>
                array(
                    'id' => 100,
                    'title' => 'Immigration/Visa Processing',
                ),
            95 =>
                array(
                    'id' => 1109,
                    'title' => 'Importer',
                ),
            96 =>
                array(
                    'id' => 1126,
                    'title' => 'Indenting',
                ),
            97 =>
                array(
                    'id' => 55,
                    'title' => 'Indenting Firm',
                ),
            98 =>
                array(
                    'id' => 2125,
                    'title' => 'Individual/Personal Recruitment',
                ),
            99 =>
                array(
                    'id' => 1123,
                    'title' => 'Industrial Machineries (Generator, Diesel Engine etc.)',
                ),
            100 =>
                array(
                    'id' => 2,
                    'title' => 'Insurance',
                ),
            101 =>
                array(
                    'id' => 1102,
                    'title' => 'Interior Design',
                ),
            102 =>
                array(
                    'id' => 104,
                    'title' => 'Inventory/Warehouse',
                ),
            103 =>
                array(
                    'id' => 4,
                    'title' => 'Investment/Merchant Banking',
                ),
            104 =>
                array(
                    'id' => 19,
                    'title' => 'ISP',
                ),
            105 =>
                array(
                    'id' => 17,
                    'title' => 'IT Enabled Service',
                ),
            106 =>
                array(
                    'id' => 1110,
                    'title' => 'Jewelry/Gem',
                ),
            107 =>
                array(
                    'id' => 77,
                    'title' => 'Jute Goods/ Jute Yarn',
                ),
            108 =>
                array(
                    'id' => 1134,
                    'title' => 'Kindergarten',
                ),
            109 =>
                array(
                    'id' => 113,
                    'title' => 'Lamps',
                ),
            110 =>
                array(
                    'id' => 2119,
                    'title' => 'Land Phone',
                ),
            111 =>
                array(
                    'id' => 57,
                    'title' => 'Law Firm',
                ),
            112 =>
                array(
                    'id' => 3,
                    'title' => 'Leasing',
                ),
            113 =>
                array(
                    'id' => 1099,
                    'title' => 'Livestock',
                ),
            114 =>
                array(
                    'id' => 26,
                    'title' => 'Logistic/Courier/Air Express Companies',
                ),
            115 =>
                array(
                    'id' => 2122,
                    'title' => 'LPG Gas/Cylinder Gas',
                ),
            116 =>
                array(
                    'id' => 1135,
                    'title' => 'Madrasa',
                ),
            117 =>
                array(
                    'id' => 53,
                    'title' => 'Manpower Recruitment',
                ),
            118 =>
                array(
                    'id' => 6,
                    'title' => 'Manufacturing (FMCG)',
                ),
            119 =>
                array(
                    'id' => 7,
                    'title' => 'Manufacturing (Light Engineering & Heavy Industry)',
                ),
            120 =>
                array(
                    'id' => 33,
                    'title' => 'Market Research Firms',
                ),
            121 =>
                array(
                    'id' => 62,
                    'title' => 'Medical Equipment',
                ),
            122 =>
                array(
                    'id' => 118,
                    'title' => 'Medical Equipment',
                ),
            123 =>
                array(
                    'id' => 1132,
                    'title' => 'Micro-Credit',
                ),
            124 =>
                array(
                    'id' => 2108,
                    'title' => 'Mineral Water',
                ),
            125 =>
                array(
                    'id' => 1127,
                    'title' => 'Mining',
                ),
            126 =>
                array(
                    'id' => 80,
                    'title' => 'Mobile Accessories',
                ),
            127 =>
                array(
                    'id' => 93,
                    'title' => 'Motel',
                ),
            128 =>
                array(
                    'id' => 1124,
                    'title' => 'Motor Vehicle body manufacturer',
                ),
            129 =>
                array(
                    'id' => 1125,
                    'title' => 'Motor Workshop',
                ),
            130 =>
                array(
                    'id' => 28,
                    'title' => 'Multinational Companies',
                ),
            131 =>
                array(
                    'id' => 2096,
                    'title' => 'Museum',
                ),
            132 =>
                array(
                    'id' => 29,
                    'title' => 'Newspaper/Magazine',
                ),
            133 =>
                array(
                    'id' => 22,
                    'title' => 'NGO',
                ),
            134 =>
                array(
                    'id' => 1115,
                    'title' => 'Online Newspaper/ News Portal',
                ),
            135 =>
                array(
                    'id' => 65,
                    'title' => 'Overseas Companies',
                ),
            136 =>
                array(
                    'id' => 74,
                    'title' => 'Packaging Industry',
                ),
            137 =>
                array(
                    'id' => 88,
                    'title' => 'Paint',
                ),
            138 =>
                array(
                    'id' => 84,
                    'title' => 'Paper',
                ),
            139 =>
                array(
                    'id' => 2103,
                    'title' => 'Park',
                ),
            140 =>
                array(
                    'id' => 2095,
                    'title' => 'Party/ Community Center',
                ),
            141 =>
                array(
                    'id' => 2110,
                    'title' => 'Pest Control',
                ),
            142 =>
                array(
                    'id' => 40,
                    'title' => 'Pharmaceutical/Medicine Companies',
                ),
            143 =>
                array(
                    'id' => 83,
                    'title' => 'Physiotherapy center',
                ),
            144 =>
                array(
                    'id' => 73,
                    'title' => 'Plastic/ Polymer Industry',
                ),
            145 =>
                array(
                    'id' => 103,
                    'title' => 'Port Service',
                ),
            146 =>
                array(
                    'id' => 120,
                    'title' => 'Pottery',
                ),
            147 =>
                array(
                    'id' => 60,
                    'title' => 'Poultry',
                ),
            148 =>
                array(
                    'id' => 1131,
                    'title' => 'Power',
                ),
            149 =>
                array(
                    'id' => 1118,
                    'title' => 'Professional Photographers',
                ),
            150 =>
                array(
                    'id' => 2118,
                    'title' => 'PSTN',
                ),
            151 =>
                array(
                    'id' => 30,
                    'title' => 'Public Relation Companies',
                ),
            152 =>
                array(
                    'id' => 2116,
                    'title' => 'Radio',
                ),
            153 =>
                array(
                    'id' => 63,
                    'title' => 'Real Estate',
                ),
            154 =>
                array(
                    'id' => 2102,
                    'title' => 'Religious Place',
                ),
            155 =>
                array(
                    'id' => 1097,
                    'title' => 'Reptile Firms',
                ),
            156 =>
                array(
                    'id' => 72,
                    'title' => 'Research Organization',
                ),
            157 =>
                array(
                    'id' => 92,
                    'title' => 'Resort',
                ),
            158 =>
                array(
                    'id' => 42,
                    'title' => 'Restaurant',
                ),
            159 =>
                array(
                    'id' => 9,
                    'title' => 'Retail Store',
                ),
            160 =>
                array(
                    'id' => 2112,
                    'title' => 'Salt',
                ),
            161 =>
                array(
                    'id' => 85,
                    'title' => 'Sanitary ware',
                ),
            162 =>
                array(
                    'id' => 89,
                    'title' => 'Satellite TV',
                ),
            163 =>
                array(
                    'id' => 1133,
                    'title' => 'School',
                ),
            164 =>
                array(
                    'id' => 1101,
                    'title' => 'Science Laboratory',
                ),
            165 =>
                array(
                    'id' => 56,
                    'title' => 'Security Service',
                ),
            166 =>
                array(
                    'id' => 71,
                    'title' => 'Share Brokerage/ Securities House',
                ),
            167 =>
                array(
                    'id' => 25,
                    'title' => 'Shipping',
                ),
            168 =>
                array(
                    'id' => 108,
                    'title' => 'Shipyard',
                ),
            169 =>
                array(
                    'id' => 2094,
                    'title' => 'Shopping mall',
                ),
            170 =>
                array(
                    'id' => 46,
                    'title' => 'Shrimp',
                ),
            171 =>
                array(
                    'id' => 16,
                    'title' => 'Software Company',
                ),
            172 =>
                array(
                    'id' => 1093,
                    'title' => 'Spinning',
                ),
            173 =>
                array(
                    'id' => 2100,
                    'title' => 'Sports Complex',
                ),
            174 =>
                array(
                    'id' => 107,
                    'title' => 'Steel',
                ),
            175 =>
                array(
                    'id' => 1107,
                    'title' => 'Super store',
                ),
            176 =>
                array(
                    'id' => 105,
                    'title' => 'Supply Chain',
                ),
            177 =>
                array(
                    'id' => 79,
                    'title' => 'Sweater Industry',
                ),
            178 =>
                array(
                    'id' => 2101,
                    'title' => 'Swimming Pool',
                ),
            179 =>
                array(
                    'id' => 1111,
                    'title' => 'Tailor shop',
                ),
            180 =>
                array(
                    'id' => 45,
                    'title' => 'Tannery/Footwear',
                ),
            181 =>
                array(
                    'id' => 50,
                    'title' => 'Tea Garden',
                ),
            182 =>
                array(
                    'id' => 1122,
                    'title' => 'Technical Infrastructure',
                ),
            183 =>
                array(
                    'id' => 5,
                    'title' => 'Telecommunication',
                ),
            array(
                'id' => 36,
                'title' => 'Textile',
            ),
            array(
                'id' => 1096,
                'title' => 'Third Party Auditor (Quality, Health, Environment, Compliance',
            ),
            array(
                'id' => 86,
                'title' => 'Tiles/Ceramic',
            ),
            array(
                'id' => 69,
                'title' => 'Tobacco',
            ),
            array(
                'id' => 109,
                'title' => 'Toiletries',
            ),
            array(
                'id' => 99,
                'title' => 'Tour Operator',
            ),
            array(
                'id' => 1114,
                'title' => 'Toy',
            ),
            array(
                'id' => 24,
                'title' => 'Trading or Export/Import',
            ),
            array(
                'id' => 12,
                'title' => 'Training Institutes',
            ),
            array(
                'id' => 101,
                'title' => 'Transport Service',
            ),
            array(
                'id' => 102,
                'title' => 'Transportation',
            ),
            array(
                'id' => 44,
                'title' => 'Travel Agent',
            ),
            array(
                'id' => 112,
                'title' => 'Tyre manufacturer',
            ),
            array(
                'id' => 10,
                'title' => 'University',
            ),
            array(
                'id' => 1092,
                'title' => 'Venture Capital Firm',
            ),
            array(
                'id' => 1095,
                'title' => 'Washing Factory',
            ),
            array(
                'id' => 1113,
                'title' => 'Watch',
            ),
            array(
                'id' => 1116,
                'title' => 'Web Media/Blog',
            ),
            array(
                'id' => 8,
                'title' => 'Wholesale',
            ),
        );

        DB::table('area_of_business')->insert($areaOfBusiness);

        Schema::enableForeignKeyConstraints();

    }
}
