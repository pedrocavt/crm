<?php

namespace App\Repositories\Customer;

use Illuminate\Database\Eloquent\Model;

interface CustomerRepositoryInterface
{
    public function create(array $data): Model;
    public function delete(int $id): bool;
    public function update(int $id, array $data): ?Model;
    public function find(int $id): ?Model;
}
