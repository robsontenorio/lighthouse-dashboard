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

class StoreMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private Schema $schema;
    private array $tracing;
    private array $request;

    public function __construct(array $request, array $tracing)
    {
        $this->tracing = $tracing;
        $this->request = $request;
    }

    public function handle()
    {
        $this->schema = SyncGraphQLSchema::run();

        $operation = $this->storeOperationMetric();
        $this->storeFieldsOperationMetric($operation);
    }

    private function storeOperationMetric()
    {
        $operationName = $this->getOperationName();

        $operation = Operation::firstOrCreate([
            'schema_id' => $this->schema->id,
            'name' => $operationName
        ]);

        Tracing::create([
            'operation_id' => $operation->id,
            'request' => $this->request,
            'execution' => $this->tracing,
            'start_time' => $this->tracing['startTime'],
            'end_time' => $this->tracing['endTime'],
            'duration' => $this->tracing['duration'],
        ]);

        return $operation;
    }

    private function storeFieldsOperationMetric(Operation $operation)
    {
        $resolvers = $this->getResolvers();
        $paths = $resolvers->groupBy(fn ($item) => $item['parentType'] . '.' . $item['fieldName'])->keys();

        $paths->each(function ($path) use ($operation) {
            [$parentType, $name] = explode('.', $path);
            $type = Type::firstOrNew(['name' => $parentType]);

            $field = Field::firstOrNew([
                'type_id' => $type->id,
                'name' => $name,
            ]);

            if ($field->id == null) {
                return;
            }

            FieldsOperations::create([
                'field_id' => $field->id,
                'operation_id' => $operation->id,
                'requested_at' => now()
            ]);
        });
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
