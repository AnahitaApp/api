<?php
declare(strict_types=1);

namespace App\Models\Traits;

use App\Contracts\Models\HasValidationRulesContract;

/**
 * Class HasValidationRules
 * @package App\Models\Traits
 */
trait HasValidationRules
{
    /**
     * Get Validation Rules
     *
     * @param string $context
     * @param array $params any additional parameters passed in
     * @return array
     */
    public function getValidationRules(string $context = null, ...$params): array
    {
        $rules = [];
        $baseRules = $this->buildModelValidationRules(...$params);

        if (array_key_exists(HasValidationRulesContract::VALIDATION_RULES_BASE, $baseRules)) {

            $rules = $baseRules[HasValidationRulesContract::VALIDATION_RULES_BASE];

            foreach ($rules as $rule) {
                if (is_array($rule)) {
                    array_unshift($rule, 'bail');
                }
            }

            if (!is_null($context) && array_key_exists($context, $baseRules)) {
                foreach ($baseRules[$context] as $modifier => $keys) {
                    $modifierParts = explode('-', $modifier);
                    $position = $modifierParts[0];
                    $rule = $modifierParts[1];

                    foreach ($keys as $key) {
                        if (array_key_exists($key, $rules)) {
                            if ($position == 'prepend') {
                                array_unshift($rules[$key], $rule);
                            }
                            else {
                                $rules[$key][] = $rule;
                            }
                        }
                    }
                }
            }
        }

        return $rules;
    }
}