<?php

namespace App\Repositories\Meeting;

use App\Models\Meeting;
use App\Events\MeetingScheduled;
use Illuminate\Support\Collection;
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

    public function findAll(int $id): ?Collection
    {
        $meetings = $this->model::with(['user:id,name', 'invitedUser:id,name'])
            ->where('user_id', $id)
            ->orWhere('invited_user_id', $id)
            ->orderBy('scheduled_at', 'asc')
            ->get();
        
        return $meetings;
        
    }

}
