<?php

namespace Tests\Utils\Traits;

use App\Models\Type;

trait TypeAssertions
{

    protected Type $type;

    /**
     * Assert has a Type using a where condition.
     */
    protected function assertHasType(array $condition)
    {
        $condition +=  ['schema_id' => 1];

        $this->type = Type::where($condition)->first();

        $this->assertNotNull($this->type);

        return $this;
    }

    /**
     * Assert fields of Type match to values.
     * 
     * @param string[] $match  Array of properties for each field to be compared.
     */
    protected function withFields(array $match)
    {
        $typeFieldsValues = $this->type->fields->map(fn ($field) => $field->only(['name', 'description', 'type_def']))->toArray();

        $this->assertEqualsCanonicalizing($typeFieldsValues, $match);
    }
}
