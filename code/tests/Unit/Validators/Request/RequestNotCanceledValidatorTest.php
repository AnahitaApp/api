<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Request;

use App\Validators\Request\RequestNotCanceledValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class RequestNotCanceledValidatorTest
 * @package Tests\Unit\Validators\Request
 */
class RequestNotCanceledValidatorTest extends TestCase
{
    public function testValidateFailsRequestNotInRoute()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturnNull();

        $validator = new RequestNotCanceledValidator($request);

        $this->assertFalse($validator->validate('accept', true));
    }

    public function testValidateFailsRequestCanceled()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturn(new \App\Models\Request\Request([
            'canceled_at' => Carbon::now(),
        ]));

        $validator = new RequestNotCanceledValidator($request);

        $this->assertFalse($validator->validate('accept', true));
    }

    public function testValidatePasses()
    {
        $request = mock(Request::class);

        $request->shouldReceive('route')->once()->with('request')->andReturn(new \App\Models\Request\Request([
            'canceled_at' => null,
        ]));

        $validator = new RequestNotCanceledValidator($request);

        $this->assertTrue($validator->validate('accept', true));
    }
}