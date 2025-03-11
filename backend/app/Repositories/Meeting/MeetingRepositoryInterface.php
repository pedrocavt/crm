<?php

namespace App\Repositories\Meeting;

use Illuminate\Database\Eloquent\Model;

interface MeetingRepositoryInterface
{
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): ?Model;
    public function delete(int $id): bool;
}
