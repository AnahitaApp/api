<?php
declare(strict_types=1);

namespace Tests\Integration\Console\Commands;

use App\Console\Commands\ReindexResources;
use App\Contracts\Repositories\ResourceRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\Resource;
use App\Models\User\User;
use App\Repositories\ResourceRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ReindexResourcesTest
 * @package Tests\Integration\Console\Commands
 */
class ReindexResourcesTest extends TestCase
{
    use MocksApplicationLog, DatabaseSetupTrait;

    /**
     * @var ReindexResources
     */
    private $command;

    /**
     * @var ResourceRepositoryContract
     */
    private $resourceRepository;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->resourceRepository = new ResourceRepository(new Resource(), $this->getGenericLogMock());
        $this->userRepository = new UserRepository(
            new User(),
            $this->getGenericLogMock(),
            mock(Hasher::class),
        );

        $this->command = new ReindexResources(
            $this->resourceRepository,
            $this->userRepository,
        );
    }

    public function testIndexUsers()
    {
        User::unsetEventDispatcher();

        factory(User::class)->create();

        factory(Resource::class, 3)->create();

        $this->assertCount(3, Resource::all());
        $this->assertCount(4, User::all());

        $reflected = new \ReflectionClass($this->command);

        $output = $reflected->getProperty('output');
        $output->setAccessible(true);
        $mockOutput = mock(SymfonyStyle::class);

        $progressMock = mock(ProgressIndicator::class);
        $progressMock->shouldReceive('advance');

        $mockOutput->shouldReceive('createProgressBar')->once()->with(4)->andReturn($progressMock);

        $output->setValue($this->command, $mockOutput);

        $this->command->indexData($this->userRepository);

        $this->assertCount(4, Resource::all());
    }
}