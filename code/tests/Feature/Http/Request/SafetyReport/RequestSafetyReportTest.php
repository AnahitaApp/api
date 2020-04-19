<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Request\SafetyReport;

use App\Models\Request\Request;
use App\Models\Request\SafetyReport;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class RequestSafetyReportTest
 * @package Tests\Feature\Http\Request\SafetyReport
 */
class RequestSafetyReportTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    const BASE_PATH = '/v1/requests/';

    /**
     * @var string
     */
    private $path;

    /**
     * @var Request
     */
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function setUpRoute(array $data)
    {
        $this->request = factory(Request::class)->create($data);
        $this->path = static::BASE_PATH . $this->request->id . '/safety-reports';
    }

    public function testNotLoggedInUserBlocked()
    {
        $this->setUpRoute([]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testNotRelatedUserBlocked()
    {
        $this->actAs(Role::APP_USER);

        $this->setUpRoute([]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessfulWithRequestedUser()
    {
        $this->actAs(Role::APP_USER);
        $this->setUpRoute([
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path, [
            'description' => 'They Did Something',
        ]);

        $response->assertStatus(201);

        /** @var SafetyReport $safetyReport */
        $safetyReport = SafetyReport::first();
        $this->assertEquals($this->actingAs->id, $safetyReport->reporter_id);
        $this->assertEquals($this->request->id, $safetyReport->request_id);
        $this->assertEquals('They Did Something', $safetyReport->description);
    }

    public function testCreateSuccessfulWithCompletedUser()
    {
        $this->actAs(Role::APP_USER);
        $this->setUpRoute([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path, [
            'description' => 'They Did Something',
        ]);

        $response->assertStatus(201);

        /** @var SafetyReport $safetyReport */
        $safetyReport = SafetyReport::first();
        $this->assertEquals($this->actingAs->id, $safetyReport->reporter_id);
        $this->assertEquals($this->request->id, $safetyReport->request_id);
        $this->assertEquals('They Did Something', $safetyReport->description);
    }

    public function testCreateFailsMissingRequiredFields()
    {
        $this->actAs(Role::APP_USER);
        $this->setUpRoute([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'description' => ['The description field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidString()
    {
        $this->actAs(Role::APP_USER);
        $this->setUpRoute([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path, [
            'description' => 345,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'description' => ['The description must be a string.'],
            ]
        ]);
    }
}