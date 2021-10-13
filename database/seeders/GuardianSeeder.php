<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\YouthGuardian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuardianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('youth_guardians')->truncate();

        YouthGuardian::factory()->count(20)->create();

        Schema::enableForeignKeyConstraints();
    }
}
