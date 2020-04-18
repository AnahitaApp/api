<?php
declare(strict_types=1);

namespace App\Repositories\User;

use App\Contracts\Repositories\User\IdentificationCardRepositoryContract;
use App\Models\User\IdentificationCard;
use App\Repositories\AssetRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class IdentificationCardRepository
 * @package App\Repositories\User
 */
class IdentificationCardRepository extends AssetRepository implements IdentificationCardRepositoryContract
{
    /**
     * IdentificationCardRepository constructor.
     * @param IdentificationCard $model
     * @param LogContract $log
     * @param Filesystem $fileSystem
     * @param string $assetBaseURL
     * @param string $basePublicDirectory
     */
    public function __construct(IdentificationCard $model, LogContract $log, Filesystem $fileSystem, string $assetBaseURL, string $basePublicDirectory)
    {
        parent::__construct($model, $log, $fileSystem, $assetBaseURL, $basePublicDirectory);
    }
}