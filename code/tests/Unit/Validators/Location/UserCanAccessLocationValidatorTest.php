<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Location;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\Organization\Location;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Validators\Location\UserCanAccessLocationValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserCanAccessLocationValidatorTest
 * @package Tests\Unit\Validators\Location
 */
class UserCanAccessLocationValidatorTest extends TestCase
{
    /**
     * @var UserRepositoryContract|CustomMockInterface
     */
    private $userRepository;

    /**
     * @var Request|CustomMockInterface
     */
    private $request;

    /**
     * @var UserCanAccessLocationValidator
     */
    private UserCanAccessLocationValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = mock(UserRepositoryContract::class);
        $this->request = mock(Request::class);
        $this->validator = new UserCanAccessLocationValidator(
            $this->userRepository,
            $this->request,
        );
    }

    public function testValidateFailsWithoutLocationInRoute()
    {
        $this->request->shouldReceive('route')->andReturnNull();

        $this->assertFalse($this->validator->validate('completed_by_id', 324));
    }

    public function testValidateFailsWithoutUserBeingFound()
    {
        $location = new Location();
        $this->request->shouldReceive('route')->andReturn($location);
        $this->userRepository->shouldReceive('findOrFail')->with(324)->andReturnNull();

        $this->assertFalse($this->validator->validate('completed_by_id', 324));
    }

    public function testValidateFailsNotOrganizationManager()
    {
        $location = new Location([
            'organization' => new Organization(),
        ]);
        $user = new User([
            'organizationManagers' => new Collection([
            ]),
        ]);
        $this->request->shouldReceive('route')->andReturn($location);
        $this->userRepository->shouldReceive('findOrFail')->with(324)->andReturn($user);

        $this->assertFalse($this->validator->validate('completed_by_id', 324));
    }

    public function testValidatePasses()
    {
        $location = new Location([
            'organization' => new Organization(),
        ]);
        $location->organization->id = 3241;
        $user = new User([
            'organizationManagers' => new Collection([
                new OrganizationManager([
                    'role_id' => Role::MANAGER,
                    'organization_id' => 3241,
                ])
            ]),
        ]);
        $this->request->shouldReceive('route')->andReturn($location);
        $this->userRepository->shouldReceive('findOrFail')->with(324)->andReturn($user);

        $this->assertTrue($this->validator->validate('completed_by_id', 324));
    }
}