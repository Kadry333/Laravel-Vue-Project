<?php

namespace App\Cores\General\Repository;

use App\Cores\General\RepositoryInterfaces\FloorRepositoryInterface;
use App\Models\Floor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FloorRepository implements FloorRepositoryInterface
{
    public function get(array $withRelational = [], array $conditions = []): Collection
    {
        $query = Floor::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->get();
    }

    public function find(int $id, array $withRelational = []): ?Floor
    {
        $query = Floor::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        return $query->find($id);
    }

    public function store(array $data): Floor
    {
        return Floor::create($data);
    }

    public function update(int $id, array $data): void
    {
        Floor::findOrFail($id)->update($data);
    }

    public function delete(array $conditions): void
    {
        $query = Floor::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $query->delete();
    }

    public function paginate(int $perPage = 15, array $withRelational = [], array $conditions = []): LengthAwarePaginator
    {
        $query = Floor::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function exists(array $conditions): bool
    {
        $query = Floor::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->exists();
    }

    public function count(): int
    {
        return Floor::count();
    }

    public function first(array $conditions = [], array $withRelational = []): ?Floor
    {
        $query = Floor::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->first();
    }

    public function firstOrCreate(array $conditions, array $data = []): Floor
    {
        return Floor::firstOrCreate($conditions, $data);
    }

    public function updateOrCreate(array $conditions, array $data = []): Floor
    {
        return Floor::updateOrCreate($conditions, $data);
    }
}
