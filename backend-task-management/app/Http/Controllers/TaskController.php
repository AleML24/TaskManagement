<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Utils\ResponseFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'keyword_ids' => 'sometimes|array',
                'keyword_ids.*' => 'integer|exists:keywords,id',
                'is_done' => 'sometimes|boolean',
            ]);

            $query = Task::with('keywords');

            if (isset($validated['title'])) {
                $query->where('title', 'like', '%' . $validated['title'] . '%');
            }

            if (!empty($validated['keyword_ids'])) {
                $query->whereHas('keywords', function ($q) use ($validated) {
                    $q->whereIn('keywords.id', $validated['keyword_ids']);
                });
            }

            if (isset($validated['is_done'])) {
                $query->where('is_done', $validated['is_done']);
            }

            $tasks = $query->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'is_done' => 'boolean',
                'keyword_ids' => 'array',
                'keyword_ids.*' => 'integer|exists:keywords,id',
            ]);

            DB::beginTransaction();

            $task = Task::create($validatedData);

            if (!empty($validatedData['keyword_ids'])) {
                $task->keywords()->sync($validatedData['keyword_ids']);
            }

            DB::commit();

            return ResponseFormat::response(201, 'Task created successfully', $task->load('keywords'));
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Change the status of a task.
     * This method toggles the 'is_done' status of a task.
     */
    public function toggleStatus(Task $task)
    {
        try {
            $task->is_done = !$task->is_done;
            $task->save();

            return ResponseFormat::response(200, 'Task status toggled successfully', $task);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'is_done' => 'sometimes|boolean',
            ]);

            $task->update($validatedData);
            return ResponseFormat::response(200, 'Task updated successfully', $task);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return ResponseFormat::response(200, 'Task deleted successfully', null);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }
}
