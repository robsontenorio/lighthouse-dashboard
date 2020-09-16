<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\FieldsOperations;
use App\Models\Operation;
use App\Models\Schema;
use App\Models\Tracing;
use App\Models\Type;

class StressSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Preparing random schema...');

        $schema = Schema::first();

        $operations = Operation::factory()
            ->count(50)
            ->hasTypes()
            ->create([
                'schema_id' => $schema->id
            ]);


        // $types = factory(Type::class, 200)->create([
        //     'schema_id' => $schema->id
        // ]);

        // $types->each(function ($type) {
        //     factory(Field::class, 10)->create([
        //         'type_id' => $type->id
        //     ]);
        // });

        // $fields = Field::all();

        // $this->command->info("Go!");

        // $bar = $this->command->getOutput()->createProgressBar(1000);
        // $bar->start();

        // for ($i = 0; $i < 1000; $i++) {
        //     factory(FieldsOperations::class, 5)->create([
        //         'field_id' => $fields->random(),
        //         'operation_id' => $operations->random()
        //     ]);

        //     // factory(Tracing::class, 300)->create([
        //     //     'operation_id' => $operations->random()
        //     // ]);

        //     $bar->advance();
        // }

        // $bar->finish();
        // $this->command->info('\nDone!\n\n');
    }
}
