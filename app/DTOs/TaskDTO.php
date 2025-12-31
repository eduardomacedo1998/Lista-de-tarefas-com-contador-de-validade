<?php

namespace App\DTOs;

class TaskDTO
{
    public $title;
    public $description;
    public $priority;
    public $due_date;
    public $is_completed;
    public $user_id;

    public function __construct($title, $description = null, $priority = 1, $due_date = null, $is_completed = false, $user_id = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->due_date = $due_date;
        $this->is_completed = $is_completed;
        $this->user_id = $user_id;
    }

    public static function fromRequest($request, $userId = null)
    {
        return new self(
            $request->input('title'),
            $request->input('description'),
            $request->input('priority'),
            $request->input('due_date'),
            $request->has('is_completed') ? $request->boolean('is_completed') : null,
            $userId ?: ($request->user() ? $request->user()->id : null)
        );
    }

    public function toArray()
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'is_completed' => $this->is_completed,
            'user_id' => $this->user_id,
        ], function($value) {
            return $value !== null;
        });
    }
}
