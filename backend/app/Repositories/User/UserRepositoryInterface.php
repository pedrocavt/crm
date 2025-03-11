<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function create(array $data): Model;
}
