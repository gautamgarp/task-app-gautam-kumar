<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::query()
            ->when(request('status'), fn ($q) =>
                $q->where('status', request('status'))
            )
            ->when(request('trashed') === 'only', fn ($q) => $q->onlyTrashed())
            ->when(request('trashed') === 'with', fn ($q) => $q->withTrashed())
            ->orderBy('id', 'asc')
            ->paginate(10);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }

    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return new TaskResource($task);
    }

    public function forceDelete($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->forceDelete();

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
