<?php

namespace LighthouseDashboard\Database\Seeds;

use Illuminate\Database\Seeder;
use LighthouseDashboard\Models\Field;
use LighthouseDashboard\Models\FieldsOperations;
use LighthouseDashboard\Models\Operation;
use LighthouseDashboard\Models\Schema;
use LighthouseDashboard\Models\Tracing;
use LighthouseDashboard\Models\Type;

class StressSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Preparing random schema...');

        $schema = Schema::first();

        $operations = factory(Operation::class, 50)->create([
            'schema_id' => $schema->id
        ]);

        $types = factory(Type::class, 200)->create([
            'schema_id' => $schema->id
        ]);

        $types->each(function ($type) {
            factory(Field::class, 10)->create([
                'type_id' => $type->id
            ]);
        });

        $fields = Field::all();

        $this->command->info("Go!");

        $bar = $this->command->getOutput()->createProgressBar(1000);
        $bar->start();

        for ($i = 0; $i < 1000; $i++) {
            factory(FieldsOperations::class, 5)->create([
                'field_id' => $fields->random(),
                'operation_id' => $operations->random()
            ]);

            // factory(Tracing::class, 300)->create([
            //     'operation_id' => $operations->random()
            // ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('\nDone!\n\n');
    }
}
