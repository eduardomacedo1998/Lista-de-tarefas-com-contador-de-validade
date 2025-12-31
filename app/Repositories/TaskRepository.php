<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var Task
     */
    protected $model;

    /**
     * TaskRepository constructor.
     * @param Task $model
     */
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function getAllByUser($userId, array $filters = [])
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_completed', $filters['status'] === 'completed');
        }

        if (isset($filters['priority']) && $filters['priority'] !== '') {
            $query->where('priority', $filters['priority']);
        }

        return $query->orderBy('priority', 'desc')->orderBy('due_date', 'asc')->get();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
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
