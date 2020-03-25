<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Exceptions\NotImplementedException;
use App\Models\User\User;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
class UserRepositoryTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->hasher = $this->app->make(Hasher::class);

        $this->repository = new UserRepository(
            new User(),
            $this->getGenericLogMock(),
            $this->hasher
        );
    }

    public function testFindAllSuccess()
    {
        factory(User::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindAllOrderedByEmail()
    {
        $userT = factory(User::class)->create([
            'email' => 't@t.weg',
        ]);
        $userA = factory(User::class)->create([
            'email' => 'a@t.weg',
        ]);
        $userG = factory(User::class)->create([
            'email' => 'g@t.weg',
        ]);
        $userZ = factory(User::class)->create([
            'email' => 'z@t.weg',
        ]);
        $items = $this->repository->findAll([], [], ['email' => 'asc']);
        $this->assertCount(4, $items);
        $this->assertEquals($userA->id, $items[0]->id);
        $this->assertEquals($userG->id, $items[1]->id);
        $this->assertEquals($userT->id, $items[2]->id);
        $this->assertEquals($userZ->id, $items[3]->id);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(User::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(User::class)->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }


    public function testCreateSuccess()
    {
        $user = $this->repository->create([
            'email' => 'test@test.com',
            'name' => 'Kelly Ann Conway',
            'password' => 'Something secure',
        ]);

        $this->assertEquals(1, User::count());
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals('Kelly Ann Conway', $user->name);
        $this->assertTrue($this->hasher->check('Something secure', $user->password));
    }

    public function testUpdateSuccess()
    {
        $model = factory(User::class)->create(['email' => 'butts@butts.com']);
        $this->repository->update($model, [
            'email' => 'bump@butts.com',
            'password' => 'Something secure',
        ]);

        $updated = User::find($model->id);
        $this->assertEquals('bump@butts.com', $updated->email);
        $this->assertTrue($this->hasher->check('Something secure', $updated->password));
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new User());
    }

    public function testFindByEmailSuccess()
    {
        $user = factory(User::class)->create([
            'email' => 'test@test.com',
        ]);

        $result = $this->repository->findByEmail('test@test.com');

        $this->assertEquals($user->id, $result->id);
    }

    public function testFindByEmailReturnsNull()
    {
        $this->assertNull($this->repository->findByEmail('test@test.com'));
    }
}