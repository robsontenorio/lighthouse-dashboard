<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\FieldsOperations;
use App\Models\Operation;
use App\Models\Request;
use App\Models\Schema;
use App\Models\Tracing;
use App\Models\Type;
use Illuminate\Foundation\Testing\WithFaker;

class LighthouseDashboardSeeder extends Seeder
{
    use WithFaker;

    private $times = 100000;

    public function run()
    {
        $this->command->info('Preparing random schema ...');

        $schema = Schema::first();

        $clients = Client::factory()->times(10)->create();

        $queryType = Type::factory()
            ->ofQueryType()
            ->has(Field::factory()->count(50))
            ->create([
                'schema_id' => $schema->id
            ]);

        Type::factory()
            ->count(150)
            ->has(Field::factory()->count(5))
            ->create([
                'schema_id' => $schema->id
            ]);

        $queryType->fields()->each(function ($field) {
            Operation::factory()->create(['field_id' => $field]);
        });

        $operations = Operation::all();
        $fields = Field::all();


        $this->command->info("Preparing chunk with {$this->times} requests ...\n");
        $bar = $this->command->getOutput()->createProgressBar($this->times);
        $bar->start();

        for ($i = 0; $i < $this->times; $i++) {
            $operation = $operations->random();
            $client = $clients->random();
            $field = $fields->random();
            $requested_at = $this->makeFaker()->dateTimeBetween('last month');
            $duration = $this->makeFaker()->randomNumber(8);

            $operation_requests[] = [
                'field_id' => $field->id,
                'operation_id' => $operation->id,
                'client_id' => $client->id,
                'requested_at' => $requested_at,
                'duration' => $this->makeFaker()->randomElement([null, $duration])
            ];

            $bar->advance();

            // Log operation
            // Request::factory()
            //     ->has(Tracing::factory())
            //     ->create([
            //         'field_id' => $operation->field_id,
            //         'operation_id' => $operation,
            //         'client_id' => $client,
            //         'requested_at' => $requested_at,
            //     ]);

            // // Log fields of operation
            // $operation->field->type
            //     ->fields()
            //     ->each(fn ($field) => Request::factory()->times(3)->create([
            //         'field_id' => $field,
            //         'operation_id' => $operation,
            //         'client_id' => $client,
            //         'requested_at' => $requested_at,
            //         'duration' => null
            //     ]));

        }

        $bar->finish();

        $this->command->info("\n\nSeeding ...\n");

        $bar = $this->command->getOutput()->createProgressBar($this->times);
        $bar->start();

        $chunks = array_chunk($operation_requests, 10000);

        foreach ($chunks as $chunk) {
            Request::insert($chunk);
            // Tracing::insert($chunk['tracings']);
            $bar->advance(10000);
        }

        $bar->finish();
        $this->command->info("\n");
    }
}
