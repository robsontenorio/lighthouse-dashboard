<?php

namespace App\Actions;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Field;
use App\Models\Operation;
use App\Models\Request;
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

    private Client $client;
    private Schema $schema;
    private string $payload;
    private array $tracing;
    private Operation $operation;
    private $request_at;

    public function __construct(Client $client, Schema $schema, string $payload, array $tracing)
    {
        $this->client = $client;
        $this->schema = $schema;
        $this->payload = $payload;
        $this->tracing = $tracing;
        $this->requested_at = now();
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

        $this->storeFieldMetrics();
    }

    private function storeFieldMetrics(): void
    {
        $this->getResolvers()
            ->each(function ($path) {
                $field = $this->getfieldByPath($path);

                $request = Request::create([
                    'field_id' => $field->id,
                    'client_id' => $this->client->id,
                    'operation_id' => $this->operation->id,
                    'requested_at' => $this->requested_at
                ]);

                // Tracing only if field is the operation
                if ($field->id === $this->operation->field_id) {
                    $request->update(['duration' => $this->tracing['duration']]);
                    $this->storeTracing($request);
                }
            });
    }

    private function storeTracing(Request $request): void
    {
        Tracing::create([
            'request_id' => $request->id,
            'operation_id' => $this->operation->id,
            'payload' => $this->payload,
            'execution' => $this->tracing,
            'start_time' => $this->tracing['startTime'],
            'end_time' => $this->tracing['endTime'],
            'duration' => $this->tracing['duration'],
        ]);
    }

    private function getResolvers(): Collection
    {
        return collect($this->tracing['execution']['resolvers'])
            ->map(fn ($item) => ['parentType' => $item['parentType'], 'fieldName' => $item['fieldName']])
            ->unique();
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
