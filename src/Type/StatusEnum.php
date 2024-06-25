<?php
/**
 * EPI License.
 */

namespace App\Type;

/**
 * Enum for Bug Status with methods to convert between the database (int) representation and the php enum representation.
 */
enum StatusEnum: string
{
    case CLOSED = 'Closed';
    case UNRESOLVED = 'Unresolved';
    case RESOLVED = 'Resolved';

    /**
     * @param StatusEnum $value the status
     *
     * @return int status in int form from 0-2
     *
     * converts the status enum key to int
     */
    public static function convertToInt(StatusEnum $value): int
    {
        return match ($value) {
            StatusEnum::CLOSED => 0,
            StatusEnum::UNRESOLVED => 1,
            StatusEnum::RESOLVED => 2,
        };
    }

    /**
     * @param int $value the status
     *
     * @return StatusEnum
     *
     * converts the status int to enum key
     */
    public static function intToKey($value): StatusEnum
    {
        return match ((int) $value) {
            0 => StatusEnum::CLOSED,
            1 => StatusEnum::UNRESOLVED,
            2 => StatusEnum::RESOLVED,
        };
    }
}
