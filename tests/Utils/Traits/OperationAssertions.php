<?php

namespace Tests\Utils\Traits;

use App\Models\Field;
use App\Models\Operation;
use App\Models\Type;

trait OperationAssertions
{
    protected Operation $operation;

    public function assertOperationStored(string $name): self
    {
        $field = Field::where(['name' => $name, 'type_id' => 1])->first();

        $operations = Operation::where(['field_id' => $field->id])->get();

        $this->operation = $operations->first();

        $this->assertEquals($operations->count(), 1);
        $this->assertNotNull($this->operation);

        return $this;
    }

    public function withRequestsCount(int $total): self
    {
        $requests = $this->operation->requests()->whereNotNull('duration')->get();
        $this->assertEquals($requests->count(), $total);

        return $this;
    }

    public function withTracingsCount(int $total): self
    {
        $tracingsCount = $this->operation->tracings()->count();
        $this->assertEquals($tracingsCount, $total);

        return $this;
    }
}
