<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Request;

use App\Validators\Request\RequestNotAcceptedValidator;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class RequestNotAcceptedValidatorTest
 * @package Tests\Unit\Validators\Request
 */
class RequestNotAcceptedValidatorTest extends TestCase
{
    public function testValidateFailsRequestNotInRoute()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturnNull();

        $validator = new RequestNotAcceptedValidator($request);

        $this->assertFalse($validator->validate('accept', true));
    }

    public function testValidateFailsRequestAccepted()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturn(new \App\Models\Request\Request([
            'completed_by_id' => 3254,
        ]));

        $validator = new RequestNotAcceptedValidator($request);

        $this->assertFalse($validator->validate('accept', true));
    }

    public function testValidatePasses()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturn(new \App\Models\Request\Request([
            'completed_by_id' => null,
        ]));

        $validator = new RequestNotAcceptedValidator($request);

        $this->assertTrue($validator->validate('accept', true));
    }
}