<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\StringHelperServiceContract;

/**
 * Class StringHelperService
 * @package App\Providers
 */
class StringHelperService implements StringHelperServiceContract
{
    /**
     * Handles a multibyte string replacement properly
     *
     * @param $original
     * @param $replacement
     * @param $position
     * @param $length
     * @return mixed|string
     */
    public function mbSubstrReplace($original, $replacement, $position, $length)
    {
        $startString = mb_substr($original, 0, $position, "UTF-8");
        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

        $out = $startString . $replacement . $endString;

        return $out;
    }
}
