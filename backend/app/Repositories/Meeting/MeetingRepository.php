<?php

namespace App\Repositories\Meeting;

use App\Models\Meeting;
use App\Repositories\AbstractEloquentRepository;

class MeetingRepository extends AbstractEloquentRepository implements MeetingRepositoryInterface
{
    public function __construct(Meeting $meeting)
    {
        return parent($meeting)
}
