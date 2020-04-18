<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Request;

use App\Models\Request\SafetyReport;
use Tests\TestCase;

/**
 * Class SafetyReportTest
 * @package Tests\Unit\Models\Request
 */
class SafetyReportTest extends TestCase
{
    public function testReporter()
    {
        $model = new SafetyReport();
        $relation = $model->reporter();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('safety_reports.reporter_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRequest()
    {
        $model = new SafetyReport();
        $relation = $model->request();

        $this->assertEquals('requests.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('safety_reports.request_id', $relation->getQualifiedForeignKeyName());
    }
}