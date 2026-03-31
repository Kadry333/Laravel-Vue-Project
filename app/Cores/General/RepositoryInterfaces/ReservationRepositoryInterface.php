<?php

namespace App\Cores\General\RepositoryInterfaces;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReservationRepositoryInterface
{
    public function get(array $withRelational = [], array $conditions = []): Collection;

    public function find(int $id, array $withRelational = []): ?Reservation;

    public function store(array $data): Reservation;

    public function update(int $id, array $data): void;

    public function delete(array $conditions): void;

    public function paginate(int $perPage = 10, array $withRelational = [], array $conditions = []): LengthAwarePaginator;

    public function exists(array $conditions): bool;

    public function count(): int;

    public function first(array $conditions = [], array $withRelational = []): ?Reservation;

    public function firstOrCreate(array $conditions, array $data = []): Reservation;

    public function updateOrCreate(array $conditions, array $data = []): Reservation;

    public function sum(string $column, array $conditions = []): float;
}
