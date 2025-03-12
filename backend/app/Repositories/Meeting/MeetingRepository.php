<?php

namespace App\Repositories\Meeting;

use App\Models\Meeting;
use App\Events\MeetingScheduled;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AbstractEloquentRepository;

class MeetingRepository extends AbstractEloquentRepository implements MeetingRepositoryInterface
{
    public function __construct(Meeting $meeting)
    {
        parent::__construct($meeting);
    }

    public function create(array $data): Model
    {
        $meeting = parent::create($data);

        event(new MeetingScheduled($meeting));

        return $meeting;
    }

}
