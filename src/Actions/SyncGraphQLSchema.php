<?php

namespace App\Actions;

use App\Models\Schema;
use App\Models\Field;
use App\Models\Type;
use Nuwave\Lighthouse\GraphQL;
use GraphQL\Type\Schema as GraphQLSchema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as DefinitionType;
use GraphQL\Type\Introspection;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Support\Arr;

/**
 * Parse and store current GraphQL Schema.
 */
class SyncGraphQLSchema
{
    private GraphQLSchema $graphQLSchema;
    private string $schemaString;

    public function __construct()
    {
        $this->graphQLSchema = app(GraphQL::class)->prepSchema();
        $this->schemaString = SchemaPrinter::doPrint($this->graphQLSchema);
    }

    public static function run()
    {
        $self = new self;

        return $self->sync();
    }

    private function sync()
    {
        $schema = Schema::first();

        $current_hash = md5($this->schemaString);

        if ($schema->hash === $current_hash) {
            return $schema;
        }

        $schema->update([
            'hash' => $current_hash,
            'schema' => $this->schemaString
        ]);

        $types = $this->getTypes();

        foreach ($types as $name => $introspectedType) {
            $fields = $introspectedType->getFields();

            $type = Type::firstOrCreate([
                'schema_id' => $schema->id,
                'name' => $name,
            ]);

            $type->update([
                'description' => $introspectedType->description
            ]);

            foreach ($fields as $field) {
                $name = $field->name;
                $description = $field->description;
                $typeDef = isset($field->config['type']->ofType) ? $field->config['type']->ofType->name : $field->config['type']->name;

                $field = Field::firstOrCreate([
                    'type_id' => $type->id,
                    'name' => $name,
                    'type_def' => $typeDef
                ]);

                $field->update([
                    'description' => $description
                ]);
            }
        }

        return $schema;
    }

    private function getTypes()
    {
        $internalTypes = DefinitionType::getStandardTypes() + Introspection::getTypes();
        $allTypes = collect($this->graphQLSchema->getTypeMap())->reject(fn ($type) => !$type instanceof ObjectType);

        return Arr::except($allTypes, collect($internalTypes)->keys()->toArray());
    }
}
