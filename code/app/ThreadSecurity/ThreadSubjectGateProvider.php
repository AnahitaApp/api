<?php
declare(strict_types=1);

namespace App\ThreadSecurity;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class ThreadSubjectGateProvider
 * @package App\ThreadSecurity
 */
class ThreadSubjectGateProvider implements ThreadSubjectGateProviderContract
{
    /**
     * @var Application
     */
    private $app;

    /**
     * ThreadSubjectGateProvider constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Creates the gate for the passed in subject type
     *
     * @param $subjectType
     * @return ThreadSubjectGateContract|null
     */
    public function createGate($subjectType): ?ThreadSubjectGateContract
    {
        switch ($subjectType) {
            case 'general':
                return new GeneralThreadGate();
            case 'private_message':
                return new PrivateThreadGate();

            // put application level gates below
        }

        return null;
    }
}