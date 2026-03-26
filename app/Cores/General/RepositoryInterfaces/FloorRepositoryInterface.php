<?php

namespace App\Cores\General\RepositoryInterfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Floor;

interface FloorRepositoryInterface

{
    public function get(array $withRelational = [], array $conditions = []): Collection;
    public function find(int $id, array $withRelational = []): ?Floor;
    public function store(array $data): Floor;
    public function update(int $id, array $data): void;
    public function delete(array $conditions): void;
    public function paginate(int $perPage = 15, array $withRelational = [], array $conditions = []): LengthAwarePaginator;
    public function exists(array $conditions): bool;
    public function count(): int;
    public function first(array $conditions = [], array $withRelational = []): ?Floor;
    public function firstOrCreate(array $conditions, array $data = []): Floor;
    public function updateOrCreate(array $conditions, array $data = []): Floor;
}
