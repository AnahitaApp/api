<?php
declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface as LogContract;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Models\User\Message;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented as NotImplemented;

/**
 * Class MessageRepository
 * @package App\Repositories\User
 */
class MessageRepository extends BaseRepositoryAbstract implements MessageRepositoryContract
{
    use NotImplemented\Delete, NotImplemented\FindOrFail;

    /**
     * MessageRepository constructor.
     * @param Message $model
     * @param LogContract $log
     */
    public function __construct(Message $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Overrides to make sure to use the related model for the to field
     *
     * @param array $data
     * @param User|BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        if ($relatedModel) {
            $data['to_id'] = $relatedModel->id;
        }

        return parent::create($data, null, $forcedValues);
    }

    /**
     * Sends an email directly to a user
     *
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param null $greeting
     * @return Message|BaseModelAbstract
     */
    public function sendEmailToUser(User $user, string $subject, string $template, array $baseTemplateData = [], $greeting = null): Message
    {
        return $this->create([
            'subject' => $subject,
            'template' => $template,
            'email' => $user->email,
            'data' => array_merge($baseTemplateData, [
                'greeting' => $greeting ?? 'Hello ' . $user->first_name,
            ]),
        ], $user);
    }

    /**
     * Find all
     *
     * @param array $filters
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param int|null $limit pass null to get all
     * @param array $belongsToArray array of models this should belong to
     * @param int $pageNumber
     * @return LengthAwarePaginator|Collection
     */
    public function findAll(array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $pageNumber = 1)
    {
        $query = $this->buildFindAllQuery($filters, $searches, $orderBy, $with, $belongsToArray);

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            return $query->paginate($limit, $columns = ['*'], $pageName = 'page', $pageNumber);
        }
        return $query->get();
    }

    /**
     * @SWG\Definition(
     *     definition="Messages",
     *     @SWG\Property(
     *         property="data",
     *         description="A list of message models",
     *         type="array",
     *         minItems=0,
     *         maxItems=100,
     *         uniqueItems=true,
     *         @SWG\Items(ref="#/definitions/Message")
     *     )
     * )
     */
}