<?php

namespace App\Actions;

use App\Models\Field;
use App\Models\Schema;
use App\Models\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as DefinitionType;
use GraphQL\Type\Introspection;
use GraphQL\Type\Schema as GraphQLSchema;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\GraphQL;

/**
 * Sync between current introspected GraphQL schema and previous stored.
 *
 */
class SyncGraphQLSchema
{
    private Schema $schema;
    private GraphQLSchema $graphQLSchema;
    private string $schemaString;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
        $this->graphQLSchema = app(GraphQL::class)->prepSchema();
        $this->schemaString = SchemaPrinter::doPrint($this->graphQLSchema);
    }

    public static function run(Schema $schema)
    {
        $self = new self($schema);

        $self->sync();
    }

    private function sync()
    {
        $current_hash = md5($this->schemaString);

        if ($this->schema->hash === $current_hash) {
            return;
        }

        $this->schema->update([
            'hash' => $current_hash,
            'schema' => $this->schemaString
        ]);

        $this->syncTypes();
    }

    private function syncTypes()
    {
        $introspectedTypes = $this->getIntrospectedSchemaTypes();

        foreach ($introspectedTypes as $introspectedType) {
            $type = Type::updateOrCreate(
                [
                    'schema_id' => $this->schema->id,
                    'name' => $introspectedType->name,
                ],
                [
                    'description' => $introspectedType->description
                ]
            );

            $this->syncFieldsBetween($type, $introspectedType);
        }
    }

    private function syncFieldsBetween(Type $type, ObjectType $introspectedType)
    {
        $fields = $introspectedType->getFields();

        foreach ($fields as $field) {
            Field::updateOrCreate(
                [
                    'type_id' => $type->id,
                    'name' => $field->name,
                    'type_def' => (string) $field->getType(),
                ],
                [
                    'description' => $field->description,
                    'args' => $this->formatFieldArgs($field->args)
                ]
            );
        }
    }

    // TODO: make it work from Lighthouse, not from Webonyx
    private function getIntrospectedSchemaTypes()
    {
        $internalTypes = DefinitionType::getStandardTypes() + Introspection::getTypes();
        $allTypes = collect($this->graphQLSchema->getTypeMap())->reject(fn ($type) => !$type instanceof ObjectType);

        return Arr::except($allTypes, collect($internalTypes)->keys()->toArray());
    }

    // TODO: use json payload, then format it on frontend
    private function formatFieldArgs(array $args = [])
    {
        return collect($args)
            ->transform(function ($arg) {
                return '<div><span class="arg-name">'.$arg->name.'</span>: <span class="arg-type">'.(string) $arg->getType().'</span></div>';
            })
            ->implode(' ');
    }
}
