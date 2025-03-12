<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Repositories\Meeting\MeetingRepositoryInterface;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    private MeetingRepositoryInterface $meetingRepository;

    public function __construct(MeetingRepositoryInterface $meetingRepository)
    {
        $this->meetingRepository = $meetingRepository;
    }

    public function index()
    {
        return response()->json($this->meetingRepository->findAll(auth()->id()));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'invited_user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $meeting = $this->meetingRepository->create($request->all());

        return response()->json($meeting, 201);
    }

    public function show($id)
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        return response()->json($meeting);
    }

    public function update(Request $request, $id)
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'invited_user_id' => 'required|exists:users,id',
            'scheduled_at' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $meeting = $this->meetingRepository->update($id, $request->all());

        return response()->json($meeting);
    }

    public function destroy($id)
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $this->meetingRepository->delete($id);

        return response()->json(['message' => 'Reunião excluída com sucesso']);
    }
}
