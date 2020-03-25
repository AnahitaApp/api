<?php
declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\BaseValidatorAbstract;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BaseValidatorAbstractTest
 * @package Tests\Unit\Validators
 */
class BaseValidatorAbstractTest extends TestCase
{
    public function testEnsureValidatorAttributePasses()
    {
        /** @var BaseValidatorAbstract $validator */
        $validator = $this->getMockForAbstractClass(BaseValidatorAbstract::class);

        $validator->ensureValidatorAttribute('hello', 'hello');
    }

    public function testEnsureValidatorAttributeThrowsException()
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseValidatorAbstract $validator */
        $validator = $this->getMockForAbstractClass(BaseValidatorAbstract::class);

        $validator->ensureValidatorAttribute('hello', 'hi');
    }
}