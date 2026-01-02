<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class ActivityLoggerTest extends TestCase
{
    public function test_logger_methods_do_not_throw()
    {
        $logger = new ActivityLogger();

        $model = new class extends Model {
            public $id = 1;
            public $name = 'x';
            public function getAttributes()
            {
                return ['id' => $this->id, 'name' => $this->name];
            }
        };

        // Ensure methods run without exceptions; we don't assert DB writes here.
        $logger->logCreateOrUpdate($model, 'created', ['name' => ['old' => null, 'new' => 'x']], null);
        $logger->logDelete($model, $model->getAttributes(), null);

        $this->assertTrue(true);
    }
}
