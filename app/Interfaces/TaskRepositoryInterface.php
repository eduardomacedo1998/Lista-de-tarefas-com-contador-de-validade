<?php

namespace App\Interfaces;

interface TaskRepositoryInterface
{
    public function getAllByUser($userId, array $filters = []);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function clearAllByUser($userId);
}
