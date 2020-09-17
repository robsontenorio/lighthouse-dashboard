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
use Illuminate\Support\Collection;

/**
 * Parses current operation and store related metrics.
 */
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

    private function storeOperationMetrics(): void
    {
        $this->operation = Operation::firstOrCreate([
            'field_id' => $this->getFieldAsOperation()->id,
        ]);

        $this->storeTracing();
        $this->storeFieldMetrics();
    }

    private function storeTracing(): void
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

    private function storeFieldMetrics(): void
    {
        $this->getResolvers()
            ->each(function ($path) {
                $field = $this->getfieldByPath($path);

                FieldsOperations::create([
                    'field_id' => $field->id,
                    'operation_id' => $this->operation->id,
                    'requested_at' => now()
                ]);
            });
    }

    private function getResolvers(): Collection
    {
        return collect($this->tracing['execution']['resolvers']);
    }

    private function getfieldByPath(array $path)
    {
        $type = Type::where('name', $path['parentType'])->first();

        return Field::query()->where(['type_id' => $type->id, 'name' => $path['fieldName']])->first();
    }

    private function getFieldAsOperation(): Field
    {
        return $this->getfieldByPath($this->getResolvers()[0]);
    }
}
