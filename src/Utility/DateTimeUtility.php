<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Utility;

use DateTimeImmutable;

final class DateTimeUtility
{
    /**
     * Get the duration between timestamp start and timestamp end.
     */
    public static function getDurationAsText(DateTimeImmutable $start, DateTimeImmutable $end): string
    {
        $content = [];

        $interval = $end->diff($start);

        $hour = $interval->h;
        $minute = $interval->i;
        $second = $interval->s;

        if ($hour > 0) {
            $content[] = sprintf('%s hour%s', $hour, $hour > 1 ? 's' : '');
        }

        if ($minute > 0) {
            $content[] = sprintf('%s minute%s', $minute, $minute > 1 ? 's' : '');
        }

        if ($second > 0) {
            $content[] = sprintf('%s second%s', $second, $second > 1 ? 's' : '');
        }

        if (count($content) > 0) {
            $value = array_pop($content);

            if (count($content) > 0) {
                return sprintf('%s and %s', implode(', ', $content), $value);
            }

            return $value;
        }

        return '0 seconds';
    }
}
