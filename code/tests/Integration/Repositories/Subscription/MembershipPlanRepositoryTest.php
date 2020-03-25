<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Repositories\Subscription\MembershipPlanRateRepository;
use App\Repositories\Subscription\MembershipPlanRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class MembershipPlanRepositoryTest
 * @package Tests\Integration\Repositories\Subscription
 */
class MembershipPlanRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var MembershipPlanRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new MembershipPlanRepository(
            new MembershipPlan(),
            $this->getGenericLogMock(),
            new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->getGenericLogMock(),
            ),
        );
    }

    public function testFindAllSuccess()
    {
        foreach (MembershipPlan::all() as $model) {
            $model->delete();
        }

        factory(MembershipPlan::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        foreach (MembershipPlan::all() as $model) {
            $model->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(MembershipPlan::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(MembershipPlan::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = $this->repository->create([
            'duration' => MembershipPlan::DURATION_YEAR,
            'name' => 'a plan',
        ]);

        $this->assertEquals(MembershipPlan::DURATION_YEAR, $membershipPlan->duration);
        $this->assertEquals('a plan', $membershipPlan->name);
    }

    public function testCreateSuccessWithCost()
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = $this->repository->create([
            'duration' => MembershipPlan::DURATION_YEAR,
            'name' => 'a plan',
            'current_cost' => 10.12,
        ]);

        $this->assertEquals(MembershipPlan::DURATION_YEAR, $membershipPlan->duration);
        $this->assertEquals('a plan', $membershipPlan->name);
        $this->assertEquals(10.12, $membershipPlan->current_cost);
    }

    public function testUpdateSuccess()
    {
        $model = factory(MembershipPlan::class)->create([
            'name' => 'a plan'
        ]);
        $this->repository->update($model, [
            'name' => 'the same plan',
        ]);

        $updated = MembershipPlan::find($model->id);
        $this->assertEquals('the same plan', $updated->name);
    }

    public function testUpdateSuccessWithCost()
    {
        $model = factory(MembershipPlan::class)->create([
            'name' => 'a plan'
        ]);
        factory(MembershipPlanRate::class)->create([
            'cost' => 1.99,
            'membership_plan_id' => $model->id,
        ]);
        $this->repository->update($model, [
            'current_cost' => 3.99,
        ]);

        $updated = MembershipPlan::find($model->id);
        $this->assertEquals(3.99, $updated->current_cost);
    }

    public function testDeleteSuccess()
    {
        $model = factory(MembershipPlan::class)->create();

        $this->repository->delete($model);

        $this->assertNull(MembershipPlan::find($model->id));
    }
}