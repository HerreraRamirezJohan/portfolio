<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Swap-based reordering over a `sort_order` column.
 */
class SortOrder
{
    /**
     * Move a record one step up (-1) or down (+1) within its ordered set.
     *
     * @param  Collection<int, Model>  $rows
     */
    public static function move(Collection $rows, Model $record, int $direction): void
    {
        $rows = $rows->values();

        $index = $rows->search(fn (Model $row) => $row->is($record));

        if ($index === false) {
            return;
        }

        $target = $index + $direction;

        if ($target < 0 || $target >= $rows->count()) {
            return;
        }

        // Rewrite the whole run so duplicated or sparse sort_order values
        // (hand-edited rows, seeded data) can't make the swap a no-op.
        $reordered = $rows->all();
        [$reordered[$index], $reordered[$target]] = [$reordered[$target], $reordered[$index]];

        foreach ($reordered as $position => $row) {
            $row->forceFill(['sort_order' => $position + 1])->save();
        }
    }

    /**
     * The sort_order for a record appended to the end of a relation.
     */
    public static function next(HasMany $relation): int
    {
        return (int) $relation->max('sort_order') + 1;
    }
}
