<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllByUser($userId)
    {
        return Task::where('user_id', $userId)
            ->orderBy('is_completed')
            ->orderByDesc('priority')
            ->orderByRaw('due_date IS NULL ASC, due_date ASC')
            ->get();
    }

    public function findById($id)
    {
        return Task::findOrFail($id);
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($id, array $data)
    {
        $task = $this->findById($id);
        $task->update($data);
        return $task;
    }

    public function delete($id)
    {
        $task = $this->findById($id);
        return $task->delete();
    }

    public function clearAllByUser($userId)
    {
        return Task::where('user_id', $userId)->delete();
    }
}
