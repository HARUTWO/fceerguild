<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ActivityHistoryNormalizer;
use Illuminate\Support\Collection;

class ActivityHistoryNormalizerTest extends TestCase
{
    public function test_normalizes_activity_changes_and_attributes()
    {
        $act1 = (object) [
            'id' => 1,
            'created_at' => now(),
            'properties' => [
                'changes' => [
                    'name' => ['label' => 'Name', 'old' => 'A', 'new' => 'B'],
                ],
            ],
            'causer' => (object) ['name' => 'Tester'],
            'description' => 'changed name',
        ];

        $act2 = (object) [
            'id' => 2,
            'created_at' => now(),
            'properties' => [
                'attributes' => ['foo' => 'bar'],
            ],
            'causer' => null,
            'description' => 'created',
        ];

        $collection = collect([$act1, $act2]);

        $normalizer = new ActivityHistoryNormalizer();
        $out = $normalizer->normalize($collection, [['key' => 'foo', 'label' => 'Foo']]);

        $this->assertCount(2, $out);
        $this->assertEquals(1, $out[0]['id']);
        $this->assertNotEmpty($out[0]['rows']);
        $this->assertEquals('Foo', $out[1]['rows'][0]['field']);
    }
}
