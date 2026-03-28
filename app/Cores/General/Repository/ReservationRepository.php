<?php

namespace App\Cores\General\Repository;

use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function get(array $withRelational = [], array $conditions = []): Collection
    {
        $query = Reservation::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->get();
    }

    public function find(int $id, array $withRelational = []): ?Reservation
    {
        $query = Reservation::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        return $query->find($id);
    }

    public function store(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function update(int $id, array $data): void
    {
        Reservation::findOrFail($id)->update($data);
    }

    public function delete(array $conditions): void
    {
        $query = Reservation::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        $query->delete();
    }

    public function paginate(int $perPage = 15, array $withRelational = [], array $conditions = []): LengthAwarePaginator
    {
        $query = Reservation::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            if ($value === null || $value === '') continue;

            if ($column === 'search') {
                $query->where(function ($q) use ($value) {
                    $q->whereHas('client', function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                          ->orWhere('email', 'like', "%{$value}%");
                    })->orWhereHas('room', function ($q) use ($value) {
                        $q->where('number', 'like', "%{$value}%");
                    })->orWhere('id', 'like', "%{$value}%");
                });
            } elseif ($column === 'status') {
                $query->where('status', $value);
            } else {
                $query->where($column, $value);
            }
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function exists(array $conditions): bool
    {
        $query = Reservation::query();

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->exists();
    }

    public function count(): int
    {
        return Reservation::count();
    }

    public function first(array $conditions = [], array $withRelational = []): ?Reservation
    {
        $query = Reservation::query();

        if (!empty($withRelational)) {
            $query->with($withRelational);
        }

        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }

        return $query->first();
    }

    public function firstOrCreate(array $conditions, array $data = []): Reservation
    {
        return Reservation::firstOrCreate($conditions, $data);
    }

    public function updateOrCreate(array $conditions, array $data = []): Reservation
    {
        return Reservation::updateOrCreate($conditions, $data);
    }

    public function sum(string $column, array $conditions = []): float
    {
        $query = Reservation::query();

        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        return (float) $query->sum($column);
    }
}
