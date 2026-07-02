<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateJournalAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class JournalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $journals = $request->user()->journals()->latest()->paginate(20);
        return JournalResource::collection($journals)->response();
    }

    public function publicFeed(): JsonResponse
    {
        $journals = Journal::with('user')->where('is_public', true)->latest()->paginate(20);
        return JournalResource::collection($journals)->response();
    }

    public function store(StoreJournalRequest $request, CreateJournalAction $action): JsonResponse
    {
        $journal = $action->handle($request->user(), $request->validated());
        return (new JournalResource($journal))->response()->setStatusCode(201);
    }
}
