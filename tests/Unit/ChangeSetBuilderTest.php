<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Support\ChangeSetBuilder;

class ChangeSetBuilderTest extends TestCase
{
    public function test_detects_changed_fields_and_ignores_unchanged()
    {
        $fields = [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'meta', 'label' => 'Meta'],
        ];

        $original = [
            'name' => 'Alice',
            'meta' => ['a' => 1],
        ];

        $current = [
            'name' => 'Alice',
            'meta' => ['a' => 2],
        ];

        $changes = ChangeSetBuilder::from($fields, $original, $current);

        $this->assertArrayHasKey('meta', $changes);
        $this->assertArrayNotHasKey('name', $changes);
        $this->assertEquals('Meta', $changes['meta']['label']);
    }
}
