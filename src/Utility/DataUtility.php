<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Utility;

use Exception;
use Throwable;

final class DataUtility
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public static function assert(bool $condition, ?Throwable $throwable = null): void
    {
        if ($condition) {
            return;
        }

        if (null === $throwable) {
            throw new Exception();
        }

        throw $throwable;
    }
}
