<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\User\ProfileImage;

use App\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use App\Models\User\ProfileImage;
use App\Policies\User\ProfileImagePolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\User\ProfileImage
 */
class StoreRequest extends BaseAssetUploadRequestAbstract
{
    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ProfileImagePolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ProfileImage::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [$this->route('user')];
    }

    /**
     * @param ProfileImage $profileImage
     * @return array
     */
    public function rules(ProfileImage $profileImage): array
    {
        return $profileImage->getValidationRules(ProfileImage::VALIDATION_RULES_CREATE);
    }
}