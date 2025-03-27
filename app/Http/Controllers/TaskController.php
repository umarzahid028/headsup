<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        $tasks = Task::with(['vehicle', 'workflowStage'])->latest()->paginate(15);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        return view('tasks.create', compact('vehicles'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'workflow_stage_id' => 'required|exists:workflow_stages,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);
        
        $task = Task::create($validated);
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $vehicles = Vehicle::all();
        return view('tasks.edit', compact('task', 'vehicles'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'workflow_stage_id' => 'required|exists:workflow_stages,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        
        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
    
    /**
     * Mark a task as completed.
     */
    public function complete(Request $request, Task $task)
    {
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Task marked as completed.');
    }
}
