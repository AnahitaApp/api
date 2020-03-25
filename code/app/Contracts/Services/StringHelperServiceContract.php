<?php
declare(strict_types=1);

namespace App\Contracts\Services;

/**
 * Interface StringHelperServiceContract
 * @package App\Contracts\Services
 */
interface StringHelperServiceContract
{
    /**
     * Handles a multibyte string replace
     *
     * @param $original
     * @param $replacement
     * @param $position
     * @param $length
     * @return mixed
     */
    public function mbSubstrReplace($original, $replacement, $position, $length);
}
