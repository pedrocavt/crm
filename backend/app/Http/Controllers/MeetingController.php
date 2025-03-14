<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\Meeting\CreateMeetingRequest;
use App\Http\Requests\Meeting\UpdateMeetingRequest;
use App\Repositories\Meeting\MeetingRepositoryInterface;

class MeetingController extends Controller
{
    private MeetingRepositoryInterface $meetingRepository;

    public function __construct(MeetingRepositoryInterface $meetingRepository)
    {
        $this->meetingRepository = $meetingRepository;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->meetingRepository->findAll(auth()->id()));
    }

    public function store(CreateMeetingRequest $request)
    {
        $meeting = $this->meetingRepository->create($request->all());
        return response()->json($meeting, 201);
    }

    public function show(int $id): JsonResponse
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        return response()->json($meeting);
    }

    public function update(int $id, UpdateMeetingRequest $request): JsonResponse
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $meeting = $this->meetingRepository->update($id, $request->all());

        return response()->json($meeting);
    }

    public function destroy(int $id): JsonResponse
    {
        $meeting = $this->meetingRepository->find($id);
        if (!$meeting) {
            return response()->json(['message' => 'Reunião não encontrada'], 404);
        }

        $this->meetingRepository->delete($id);

        return response()->json(['message' => 'Reunião excluída com sucesso']);
    }
}
