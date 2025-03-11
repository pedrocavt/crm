<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\AbstractEloquentRepository;

class UserRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}
