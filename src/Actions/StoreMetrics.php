<?php

namespace App\Actions;

use App\Models\Client;
use App\Models\Error;
use App\Models\Field;
use App\Models\Operation;
use App\Models\Request;
use App\Models\Schema;
use App\Models\Tracing;
use App\Models\Type;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Events\ManipulateResult;

/**
 * Parses current operation and store related metrics.
 */
class StoreMetrics implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Client $client;
    public Schema $schema;
    public string $payload;
    public ManipulateResult $result;

    public Operation $operation;
    public array $tracing;
    public array $errors;
    public $requested_at;

    public function __construct(Client $client, Schema $schema, string $payload, ManipulateResult $result)
    {
        $this->client = $client;
        $this->schema = $schema;
        $this->payload = $payload;
        $this->result = $result;

        $this->tracing = $this->getTracing();
        $this->errors = $this->getErrors();
        $this->requested_at = now();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();
            SyncGraphQLSchema::run($this->schema);
            $this->storeOperationMetrics();
            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    private function storeOperationMetrics(): void
    {
        $field = $this->getFieldForOperation();

        // If cant parse operation field just returns. Sorry :(
        if ($field == null) {
            return;
        }

        $this->operation = Operation::firstOrCreate([
            'field_id' => $field->id,
        ]);

        // If operation has errors, log the error then return.
        if (count($this->errors)) {
            $this->storeErrors();

            return;
        }

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

                // Store tracing only if this field is a operation itself
                if ($field->is($this->operation->field)) {
                    // dump($request->id, $this->tracing['duration']);
                    $request->update(['duration' => $this->tracing['duration']]);
                    $this->storeTracing($request);
                }
            });
    }

    private function storeTracing(Request $request): void
    {
        Tracing::create([
            'request_id' => $request->id,
            'payload' => $this->payload,
            'execution' => $this->tracing,
            'start_time' => $this->tracing['startTime'],
            'end_time' => $this->tracing['endTime'],
            'duration' => $this->tracing['duration'],
            'requested_at' => $this->requested_at
        ]);
    }

    private function storeErrors()
    {
        $request = Request::create([
            'field_id' => $this->operation->field_id,
            'client_id' => $this->client->id,
            'operation_id' => $this->operation->id,
            'requested_at' => $this->requested_at,
            'duration' => $this->tracing['duration']
        ]);

        /**
         * Prevent log same message multiples times.
         * Usually when error is on node with multiples items (hasMany).
         * So the error woul be the same for each path.
         */
        $errors = collect($this->errors)->unique('message');

        foreach ($errors as $error) {
            Error::create([
                'request_id' => $request->id,
                'category' => $error->getCategory(),
                'message' => $error->getMessage(),
                'original_exception' => $error->getPrevious(),
                'body' => $error,
                'created_at' => $this->requested_at,
            ]);
        }
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

    // TODO better away? Cant get operation name from execution context.
    private function getFieldForOperation(): ?Field
    {
        $path = $this->getOperationPathFromResolvers();

        // Some errors does not include on response the path, so we need to infer the operation name
        if (!$path) {
            $path['fieldName'] = $this->inferOperationName();
            $path['parentType'] = 'Query';
        }

        return $this->getfieldByPath($path);
    }

    private function getOperationPathFromResolvers()
    {
        return isset($this->getResolvers()[0]) ? $this->getResolvers()[0] : null;
    }

    private function getTracing()
    {
        return $this->result->result->extensions['tracing'];
    }

    private function getErrors()
    {
        return $this->result->result->errors;
    }

    private function inferOperationName()
    {
        $regex = "/({)(\n*)(.*)(\n*)({)/";
        $matches = [];

        preg_match($regex, $this->payload, $matches);

        $operationName = isset($matches[3]) ? trim($matches[3]) : null;

        // if has args, remove them and get operation name only
        if (strstr($operationName, '(')) {
            $operationName = explode('(', $operationName)[0];
        }

        return $operationName;
    }
}
