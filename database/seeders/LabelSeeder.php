<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        // Avoid duplicates on re-run
        if (Label::count() > 0) {
            $this->command->info('Labels already seeded, skipping.');
            return;
        }

        Label::insert([
            [
                'key'        => 'clients',
                'name_ar'    => '\u0627\u0644\u0639\u0645\u0644\u0627\u0621',
                'name_en'    => 'Clients',
                'name_fr'    => 'Clients',
                'color'      => '#378ADD',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'employees',
                'name_ar'    => '\u0627\u0644\u0645\u0648\u0638\u0641\u0648\u0646',
                'name_en'    => 'Employees',
                'name_fr'    => 'Employ\u00e9s',
                'color'      => '#1D9E75',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'important',
                'name_ar'    => '\u0645\u0647\u0645',
                'name_en'    => 'Important',
                'name_fr'    => 'Important',
                'color'      => '#D85A30',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Labels seeded successfully.');
    }
}
