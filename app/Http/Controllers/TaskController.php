<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use App\DTOs\TaskDTO;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->middleware('auth');
        $this->taskService = $taskService;
    }

    public function index()
    {
        $tasks = $this->taskService->getUserTasks(Auth::id());
        $user = Auth::user();
        return view('dashboard', compact('tasks', 'user'));
    }

    public function dashboardFilter(Request $request)
    {
        $filters = $request->only(['status', 'priority', 'due_date']);
        $tasks = $this->taskService->getUserTasks(Auth::id(), $filters);
        $user = Auth::user();
        return view('dashboard', compact('tasks', 'user'));
    }

    public function store(StoreTaskRequest $request)
    {
        $taskDTO = TaskDTO::fromRequest($request, Auth::id());
        $this->taskService->createTask($taskDTO);

        return redirect()->back()->with('success', 'Tarefa adicionada!');
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = $this->taskService->findTask($id, Auth::id());
            $wasCompleted = $task->is_completed;

            $taskDTO = TaskDTO::fromRequest($request, Auth::id());
            $this->taskService->updateTask($id, $taskDTO);

            if ($request->has('is_completed') && $request->is_completed && !$wasCompleted) {
                return redirect()->back()->with('task_completed', true)->with('success', 'Tarefa marcada como concluída!');
            }

            return redirect()->back()->with('success', 'Tarefa atualizada!');
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                abort(403);
            }
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar tarefa.']);
        }
    }

    public function destroy($id)
    {
        try {
            $this->taskService->deleteTask($id, Auth::id());
            return redirect()->back()->with('success', 'Tarefa removida!');
        } catch (\Exception $e) {
            if ($e->getCode() === 403) {
                abort(403);
            }
            return redirect()->back()->withErrors(['error' => 'Erro ao remover tarefa.']);
        }
    }

    public function clear()
    {
        $this->taskService->clearTasks(Auth::id());
        return redirect()->back()->with('success', 'Todas as tarefas foram removidas!');
    }
}
