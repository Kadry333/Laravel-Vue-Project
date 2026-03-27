<?php

namespace App\Cores\General\RepositoryInterfaces;



use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoomRepositoryInterface
{
    public function get(array $withRelational = [], array $conditions = []): Collection;

    public function find(int $id, array $withRelational = []): ?Room;

    public function store(array $data): Room;

    public function update(int $id, array $data): void;

    public function delete(array $conditions): void;

    public function paginate(
        int $perPage = 15,
        array $withRelational = [],
        array $conditions = [],
        array $filters = []
    ): LengthAwarePaginator;

    public function exists(array $conditions): bool;

    public function count(): int;

    public function first(array $conditions = [], array $withRelational = []): ?Room;

    public function firstOrCreate(array $conditions, array $data = []): Room;

    public function updateOrCreate(array $conditions, array $data = []): Room;

    public function sum(string $column, array $conditions = []): float;
    public function getRoomsWithReservations(array $withRelational = []): Collection;
}
