<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Payment;

use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Repositories\Payment\PaymentRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class PaymentRepositoryTest
 * @package Tests\Integration\Repositories\Payment
 */
class PaymentRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var PaymentRepository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new PaymentRepository(
            new Payment(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess()
    {
        factory(Payment::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(Payment::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Payment::class)->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        /** @var Payment $payment */
        $payment = $this->repository->create([
            'amount' => 11.32,
        ], $paymentMethod);

        $this->assertEquals(11.32, $payment->amount);
        $this->assertEquals($paymentMethod->id, $payment->payment_method_id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Payment::class)->create();
        $this->repository->update($model, [
            'refunded_at' => Carbon::now(),
        ]);

        /** @var Payment $updated */
        $updated = Payment::find($model->id);
        $this->assertNotNull($updated->refunded_at);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Payment::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Payment::find($model->id));
    }
}