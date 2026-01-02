<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ReferenceDetailsBuilder;
use Illuminate\Database\Eloquent\Model;

class ReferenceDetailsBuilderTest extends TestCase
{
    public function test_builds_details_from_model_and_fields()
    {
        $model = new class extends Model {
            public $foo = 'bar';
            public $created_at;
            public $updated_at;
            public $createdBy;
            public $updatedBy;

            public function __construct()
            {
                $this->created_at = now();
                $this->updated_at = now();
                $this->createdBy = (object) ['name' => 'Creator'];
                $this->updatedBy = (object) ['name' => 'Updater'];
                parent::__construct();
            }

            public function getAttribute($key)
            {
                if ($key === 'foo') {
                    return $this->foo;
                }

                return parent::getAttribute($key);
            }
        };

        $fields = [
            ['key' => 'foo', 'label' => 'Foo'],
        ];

        $builder = new ReferenceDetailsBuilder();
        $details = $builder->build($model, $fields);

        $this->assertEquals('bar', $details['foo']);
        $this->assertArrayHasKey('_meta', $details);
        $this->assertEquals('Creator', $details['_meta']['created_by']);
    }
}
