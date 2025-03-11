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
        return response()->json(Meeting::with('customer')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $meeting = Meeting::create($request->all());

        return response()->json($meeting, 201);
    }

    public function show($id)
    {
        $meeting = Meeting::with('customer')->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        return response()->json($meeting);
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'scheduled_at' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        $meeting->update($request->all());

        return response()->json($meeting);
    }

    public function destroy($id)
    {
        $meeting = Meeting::find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $meeting->delete();

        return response()->json(['message' => 'Reunião excluída com sucesso']);
    }
}
