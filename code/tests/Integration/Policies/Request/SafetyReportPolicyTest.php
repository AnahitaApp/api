<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Request;

use App\Models\Request\Request;
use App\Models\User\User;
use App\Policies\Request\SafetyReportPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SafetyReportPolicyTest
 * @package Tests\Integration\Policies\Request
 */
class SafetyReportPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testCreateFails()
    {
        $user = factory(User::class)->create();
        $request = factory(Request::class)->create();

        $policy = new SafetyReportPolicy();

        $this->assertFalse($policy->create($user, $request));
    }

    public function testCreatePasses()
    {
        /** @var Request $request */
        $request = factory(Request::class)->create([
            'completed_by_id' => factory(User::class)->create()->id,
        ]);

        $policy = new SafetyReportPolicy();

        $this->assertTrue($policy->create($request->requestedBy, $request));
        $this->assertTrue($policy->create($request->completedBy, $request));
    }
}