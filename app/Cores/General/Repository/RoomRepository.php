<?php

namespace App\Cores\General\Repository;



use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function get(array $withRelational = [], array $conditions = []): Collection
    {
        $query = Room::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->get();
    }

    public function find(int $id, array $withRelational = []): ?Room
    {
        $query = Room::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        return $query->find($id);
    }

    public function store(array $data): Room
    {
        return Room::create($data);
    }

    public function update(int $id, array $data): void
    {
        Room::findOrFail($id)->update($data);
    }

    public function delete(array $conditions): void
    {
        $query = Room::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $query->delete();
    }

    public function paginate(
        int $perPage = 15,
        array $withRelational = [],
        array $conditions = [],
        array $filters = []
    ): LengthAwarePaginator {
        $query = Room::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        /* ================= SEARCH ================= */
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('number', 'like', "%{$search}%");
            });
        }

        /* ================= SORT ================= */
        if (!empty($filters['sort'])) {
            $direction = $filters['direction'] ?? 'asc';


            $allowedSorts = ['id', 'number', 'capacity', 'price', 'created_at'];

            if (in_array($filters['sort'], $allowedSorts)) {
                $query->orderBy($filters['sort'], $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function exists(array $conditions): bool
    {
        $query = Room::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->exists();
    }

    public function count(): int
    {
        return Room::count();
    }

    public function first(array $conditions = [], array $withRelational = []): ?Room
    {
        $query = Room::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->first();
    }

    public function firstOrCreate(array $conditions, array $data = []): Room
    {
        return Room::firstOrCreate($conditions, $data);
    }

    public function updateOrCreate(array $conditions, array $data = []): Room
    {
        return Room::updateOrCreate($conditions, $data);
    }

    public function sum(string $column, array $conditions = []): float
    {
        $query = Room::query();

        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        return $query->sum($column);
    }

    public function getRoomsWithReservations(array $withRelational = []): Collection
    {
        $query = Room::query();


        $relations = array_merge($withRelational, ['reservations:id,room_id,check_in_date,check_out_date']);
        $query->with($relations);

        return $query->get();
    }
}
