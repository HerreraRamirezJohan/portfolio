<?php

namespace App\Livewire\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Swap-based reordering over a `sort_order` column.
 */
trait ReordersRecords
{
    public function moveUp(int $id): void
    {
        $this->swap($id, -1);
    }

    public function moveDown(int $id): void
    {
        $this->swap($id, 1);
    }

    private function swap(int $id, int $direction): void
    {
        /** @var Collection<int, Model> $rows */
        $rows = $this->records()->values();

        $index = $rows->search(fn (Model $row) => $row->getKey() === $id);

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
}
