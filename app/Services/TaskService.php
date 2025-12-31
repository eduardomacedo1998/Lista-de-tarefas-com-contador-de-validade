<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\DTOs\TaskDTO;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getUserTasks($userId)
    {
        return $this->taskRepository->getAllByUser($userId);
    }

    public function createTask(TaskDTO $taskDTO)
    {
        return $this->taskRepository->create($taskDTO->toArray());
    }

    public function updateTask($id, TaskDTO $taskDTO)
    {
        return $this->taskRepository->update($id, $taskDTO->toArray());
    }

    public function deleteTask($id, $userId)
    {
        $task = $this->taskRepository->findById($id);
        
        if ($task->user_id !== $userId) {
            throw new \Exception("Unauthorized", 403);
        }

        return $this->taskRepository->delete($id);
    }

    public function clearTasks($userId)
    {
        return $this->taskRepository->clearAllByUser($userId);
    }

    public function findTask($id, $userId)
    {
        $task = $this->taskRepository->findById($id);
        
        if ($task->user_id !== $userId) {
            throw new \Exception("Unauthorized", 403);
        }

        return $task;
    }
}
