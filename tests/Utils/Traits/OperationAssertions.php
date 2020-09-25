<?php

namespace Tests\Utils\Traits;

use App\Models\Field;
use App\Models\Operation;
use App\Models\Type;

trait OperationAssertions
{
    protected Operation $operation;

    /**
     * Assert operation metric was stored.
     */
    protected function assertOperationStored(string $name): self
    {
        $field = Field::where(['name' => $name, 'type_id' => 1])->first();

        $operations = Operation::where(['field_id' => $field->id])->get();

        $this->operation = $operations->first();

        $this->assertEquals($operations->count(), 1);
        $this->assertNotNull($this->operation);

        return $this;
    }

    /**
     * Assert operation was requested "N" times.
     */
    protected function withRequestsCount(int $total): self
    {
        $requests = $this->operation->requests()->whereNotNull('duration')->get();
        $this->assertEquals($requests->count(), $total);

        return $this;
    }

    /**
     * Assert operation has "N" tracings.
     */
    protected function withTracingsCount(int $total): self
    {
        $tracingsCount = $this->operation->tracings()->count();
        $this->assertEquals($tracingsCount, $total);

        return $this;
    }

    /**
     * Get a operation by name.
     */
    protected function getOperationByName(string $name): Operation
    {
        $field = Field::where(['name' => $name, 'type_id' => 1])->first();

        return Operation::where(['field_id' => $field->id])->first();
    }
}
