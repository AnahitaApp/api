<?php
declare(strict_types=1);

namespace App\Validators\Subscription;

use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Validators\BaseValidatorAbstract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

/**
 * Class PaymentMethodIsOwnedByUserValidator
 * @package App\Validators\Subscription
 */
class PaymentMethodIsOwnedByUserValidator extends BaseValidatorAbstract
{
    /**
     * The key this is registered at
     */
    const KEY = 'payment_method_is_owned_by_user';

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var Request
     */
    private $request;

    /**
     * PaymentMethodIsOwnedByUser constructor.
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryContract $paymentMethodRepository, Request $request)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->request = $request;
    }

    /**
     * Responds to 'payment_method_is_owned_by_user', and must be attached to the token field
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $this->ensureValidatorAttribute('payment_method_id', $attribute);

        try {
            /** @var PaymentMethod $paymentMethod */
            $paymentMethod = $this->paymentMethodRepository->findOrFail($value);

            /** @var User $user */
            $user = $this->request->route('user');

            return $user->id == $paymentMethod->owner_id && $paymentMethod->owner_type == 'user';

        } catch (\Exception $e) {
            return false;
        }
    }
}