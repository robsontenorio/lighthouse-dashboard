<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\FieldsOperations;
use App\Models\Operation;
use App\Models\Schema;
use App\Models\Tracing;
use App\Models\Type;

class LighthouseDashboardSeeder extends Seeder
{
    public function run()
    {
        // TODO refresh only package migrations
        $this->command->info("Refreshing database ...");
        $this->command->call("migrate:refresh");

        $this->command->info('Preparing random schema...');

        $schema = Schema::first();

        $operations = Operation::factory()
            ->count(10)
            ->create([
                'schema_id' => $schema->id
            ]);

        $types = Type::factory()
            ->count(20)
            ->has(Field::factory()->count(5))
            ->create([
                'schema_id' => $schema->id
            ]);

        $fields = Field::all();

        $this->command->info("Seeding ...");

        $bar = $this->command->getOutput()->createProgressBar(1000);
        $bar->start();

        for ($i = 0; $i < 1000; $i++) {
            FieldsOperations::factory()->times(3)->create([
                'field_id' => $fields->random(),
                'operation_id' => $operations->random()
            ]);

            Tracing::factory()->times(10)->create([
                'operation_id' => $operations->random()
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('\nDone!\n\n');
    }
}
