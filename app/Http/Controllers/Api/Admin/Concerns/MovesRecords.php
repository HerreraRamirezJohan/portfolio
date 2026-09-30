<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use App\Support\SortOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The `PATCH {resource}/{id}/move` endpoint shared by every ordered admin
 * collection. Controllers call this, then answer with their fresh index.
 */
trait MovesRecords
{
    /**
     * The controller's full ordered record set.
     *
     * @return Collection<int, Model>
     */
    abstract protected function records(): Collection;

    protected function moveRecord(Request $request, Model $record): void
    {
        $direction = $request->validate(['direction' => 'required|in:up,down'])['direction'];

        SortOrder::move($this->records(), $record, $direction === 'up' ? -1 : 1);
    }
}
