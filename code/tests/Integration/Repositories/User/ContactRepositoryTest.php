<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Models\User\Contact;
use App\Models\User\User;
use App\Repositories\User\ContactRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ContactRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
class ContactRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var UserRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->hasher = $this->app->make(Hasher::class);

        $this->repository = new ContactRepository(
            new Contact(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess()
    {
        factory(Contact::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllSuccessWithUser()
    {
        $user = factory(User::class)->create();

        factory(Contact::class, 5)->create();

        factory(Contact::class, 4)->create([
            'requested_id' => $user->id,
        ]);
        factory(Contact::class, 3)->create([
            'initiated_by_id' => $user->id,
        ]);

        $items = $this->repository->findAll([], [], [], [], 10, [$user]);
        $this->assertCount(7, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(Contact::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Contact::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        $initiatedBy = factory(User::class)->create();
        $requested = factory(User::class)->create();

        /** @var Contact $contact */
        $contact = $this->repository->create([
            'initiated_by_id' => $initiatedBy->id,
            'requested_id' => $requested->id,
        ]);

        $this->assertEquals(1, Contact::count());
        $this->assertEquals($initiatedBy->id, $contact->initiated_by_id);
        $this->assertEquals($requested->id, $contact->requested_id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Contact::class)->create();
        $this->repository->update($model, [
            'denied_at' => Carbon::now(),
        ]);

        $updated = Contact::find($model->id);
        $this->assertNotNull($updated->denied_at);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Contact::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Contact::find($model->id));
    }
}