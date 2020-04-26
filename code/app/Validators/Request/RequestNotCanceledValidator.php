<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Validators\BaseValidatorAbstract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

/**
 * Class RequestNotCanceledValidator
 * @package App\Validators\Request
 */
class RequestNotCanceledValidator extends BaseValidatorAbstract
{
    /**
     * @var string
     */
    const KEY = 'request_not_canceled';

    /**
     * @var Request
     */
    private $request;

    /**
     * RequestNotAcceptedValidator constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * This is invoked by the validator rule 'selected_iteration_belongs_to_article'
     *
     * @param $attribute string the attribute name that is validating
     * @param $value mixed the value that we're testing
     * @param $parameters array
     * @param $validator Validator The Validator instance
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $this->ensureValidatorAttribute('accept', $attribute);

        /** @var \App\Models\Request\Request $requestModel */
        $requestModel = $this->request->route('request');

        if (!$requestModel) {
            return false;
        }

        return $requestModel->canceled_at === null;
    }
}