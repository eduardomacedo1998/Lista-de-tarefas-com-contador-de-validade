<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show tasks for the logged-in user - returns dashboard view
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            // just in case, this route is protected by auth middleware but help the analyzer
            abort(403);
        }
        $tasks = $user->tasks()
            ->orderBy('is_completed')
            ->orderByDesc('priority')
            ->orderByRaw('due_date IS NULL ASC, due_date ASC')
            ->get();

        return view('dashboard', compact('tasks'));
    }

    // store a new task
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:1|max:3',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 1,
            'due_date' => $data['due_date'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Tarefa adicionada!');
    }

    // toggle completion or update task
    public function update(Request $request, Task $task)
    {
        // ensure the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|integer|min:1|max:3',
            'due_date' => 'sometimes|nullable|date',
            'is_completed' => 'sometimes|boolean'
        ]);

        $task->update($data);

        return redirect()->back();
    }

    // destroy task
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back();
    }

    /**
     * Delete all tasks for the authenticated user.
     */
    public function clear(Request $request)
    {
        $userId = Auth::id();

        // Double-check the user
        if (! $userId) {
            abort(403);
        }

        Task::where('user_id', $userId)->delete();

        // Redirect to dashboard with success message after clearing tasks
        return redirect()->route('dashboard')->with('success', 'Todas as tarefas foram removidas.');
    }

    /**
     * Show confirmation page for clearing tasks.
     */
    public function confirmClear()
    {
        return view('tasks.confirm_clear');
    }
}
