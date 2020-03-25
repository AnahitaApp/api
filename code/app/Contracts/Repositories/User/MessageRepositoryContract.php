<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\Message;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface MessageRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface MessageRepositoryContract extends BaseRepositoryContract
{
    /**
     * Sends an email directly to a user
     *
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param string|null $greeting
     * @return Message
     */
    public function sendEmailToUser(User $user, string $subject, string $template, array $baseTemplateData = [], $greeting = null): Message;
}