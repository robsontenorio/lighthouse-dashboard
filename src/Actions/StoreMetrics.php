<?php

namespace App\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Field;
use App\Models\FieldsOperations;
use App\Models\Operation;
use App\Models\Schema;
use App\Models\Tracing;
use App\Models\Type;
use Illuminate\Queue\SerializesModels;

class StoreMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Schema $schema;
    private array $request;
    private array $tracing;
    private Operation $operation;

    public function __construct(Schema $schema, array $request, array $tracing)
    {
        $this->schema = $schema;
        $this->request = $request;
        $this->tracing = $tracing;
    }

    public function handle()
    {
        SyncGraphQLSchema::run($this->schema);

        $this->storeOperationMetrics();
    }

    private function storeOperationMetrics()
    {
        $this->operation = Operation::firstOrCreate([
            'schema_id' => $this->schema->id,
            'name' => $this->getOperationName()
        ]);

        $this->storeTracing();
        $this->storeFieldMetrics();
    }

    private function storeTracing()
    {
        Tracing::create([
            'operation_id' => $this->operation->id,
            'request' => $this->request,
            'execution' => $this->tracing,
            'start_time' => $this->tracing['startTime'],
            'end_time' => $this->tracing['endTime'],
            'duration' => $this->tracing['duration'],
        ]);
    }

    private function storeFieldMetrics()
    {
        $this->getFields()
            ->each(function ($requestedField) {
                [$parentType, $name] = explode('.', $requestedField);

                $field = Field::where([
                    'type_id' => $this->getFieldType($parentType)->id,
                    'name' => $name,
                ])->first();

                FieldsOperations::create([
                    'field_id' => $field->id,
                    'operation_id' => $this->operation->id,
                    'requested_at' => now()
                ]);
            });
    }

    private function getFieldType(string $parentType)
    {
        return Type::where('name', $parentType)->first();
    }

    private function getFields()
    {
        return $this->getResolvers()
            ->groupBy(fn ($item) => $item['parentType'] . '.' . $item['fieldName'])
            ->keys();
    }

    private function getResolvers()
    {
        return collect($this->tracing['execution']['resolvers']);
    }

    private function getOperationName()
    {
        return $this->getResolvers()[0]['path'][0];
    }
}
