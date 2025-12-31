@extends('layouts.app')

@section('title', 'Dashboard - Minhas Tarefas')

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .task-card {
            transition: transform 0.2s;
        }

        .task-card:hover {
            transform: translateY(-2px);
        }

        .priority-1 {
            border-left: 5px solid #0dcaf0;
        }

        .priority-2 {
            border-left: 5px solid #ffc107;
        }

        .priority-3 {
            border-left: 5px solid #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Sidebar / Stats -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title mb-4">Estatísticas</h5>
                    @php
                        $completed = $tasks->where('is_completed', true)->count();
                        $pending = $tasks->where('is_completed', false)->count();
                        $chartData = [
                            'labels' => ['Concluídas', 'Pendentes'],
                            'datasets' => [
                                [
                                    'data' => [$completed, $pending],
                                    'backgroundColor' => ['#198754', '#dc3545'],
                                ],
                            ],
                        ];
                    @endphp
                    <div class="chart-container">
                        <canvas id="tasksChart" data-chart='@json($chartData)'></canvas>
                    </div>
                </div>
            </div>

            <div id="session-data" data-task-completed="{{ session('task_completed') ? 'true' : 'false' }}"
                style="display:none;"></div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ações Rápidas</h5>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createTaskModal">
                            Nova Tarefa
                        </button>
                        @if ($tasks->count() > 0)
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#clearAllModal">
                                Limpar Todas
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Minhas Tarefas</h4>
                <span class="badge bg-secondary">{{ $tasks->count() }} total</span>
            </div>

            @if ($tasks->count() > 0)
                <div class="row row-cols-1 g-3">
                    @foreach ($tasks as $task)
                        <div class="col">
                            <div class="card task-card priority-{{ $task->priority }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <form method="POST" action="{{ url('/tasks/' . $task->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="is_completed"
                                                    value="{{ $task->is_completed ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-link p-0 text-decoration-none fs-4">
                                                    {!! $task->is_completed ? '✅' : '⬜' !!}
                                                </button>
                                            </form>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5
                                                class="card-title mb-1 {{ $task->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                                {{ $task->title }}
                                            </h5>
                                            <p class="card-text small text-muted mb-2">{{ $task->description }}</p>

                                            @php
                                                $days = $task->days_remaining;
                                                $badgeClass =
                                                    $days === null
                                                        ? 'bg-light text-dark'
                                                        : ($days < 0
                                                            ? 'bg-danger'
                                                            : ($days === 0
                                                                ? 'bg-warning text-dark'
                                                                : ($days <= 7
                                                                    ? 'bg-info text-dark'
                                                                    : 'bg-success')));
                                                if ($days === null) {
                                                    $badgeText = 'Sem prazo';
                                                } elseif ($days < 0) {
                                                    $badgeText = 'Vencida há ' . abs($days) . ' dias';
                                                } elseif ($days === 0) {
                                                    $badgeText = 'Vence hoje';
                                                } else {
                                                    $badgeText = 'Faltam ' . $days . ' dias';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                            <span class="badge bg-light text-dark border">
                                                Prioridade:
                                                {{ $task->priority == 3 ? 'Alta' : ($task->priority == 2 ? 'Média' : 'Baixa') }}
                                            </span>
                                        </div>
                                        <div class="ms-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                                data-task="{{ json_encode($task->only(['id', 'title', 'description', 'priority', 'due_date'])) }}">
                                                Editar
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}">
                                                Excluir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <p class="text-muted mb-0">Nenhuma tarefa encontrada. Comece criando uma!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ url('/tasks') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nova Tarefa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Prioridade</label>
                                <select name="priority" id="priority" class="form-select">
                                    <option value="1">Baixa</option>
                                    <option value="2">Média</option>
                                    <option value="3">Alta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Vencimento</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Tarefa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-form" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Tarefa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Título</label>
                            <input type="text" name="title" id="edit-title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-description" class="form-label">Descrição</label>
                            <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-priority" class="form-label">Prioridade</label>
                                <select name="priority" id="edit-priority" class="form-select">
                                    <option value="1">Baixa</option>
                                    <option value="2">Média</option>
                                    <option value="3">Alta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-due_date" class="form-label">Vencimento</label>
                                <input type="date" name="due_date" id="edit-due_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir a tarefa "<strong id="delete-task-title"></strong>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear All Confirmation Modal -->
    <div class="modal fade" id="clearAllModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Atenção!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir <strong>todas</strong> as suas tarefas? Esta ação não pode ser
                    desfeita.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('tasks.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Sim, excluir todas</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal (Task Completed) -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="fs-1 text-success mb-3">🎉</div>
                    <h4>Parabéns!</h4>
                    <p class="text-muted">Tarefa concluída com sucesso!</p>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart logic
            const chartElement = document.getElementById('tasksChart');
            if (chartElement) {
                const ctx = chartElement.getContext('2d');
                const chartData = JSON.parse(chartElement.dataset.chart);
                new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Edit logic
            const editModalElement = document.getElementById('editTaskModal');
            const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
            const editForm = document.getElementById('edit-form');

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const task = JSON.parse(this.dataset.task);
                    if (editForm) editForm.action = '/tasks/' + task.id;
                    document.getElementById('edit-title').value = task.title;
                    document.getElementById('edit-description').value = task.description || '';
                    document.getElementById('edit-priority').value = task.priority;
                    document.getElementById('edit-due_date').value = task.due_date ? task.due_date
                        .split('T')[0] : '';
                    if (editModal) editModal.show();
                });
            });

            // Delete logic
            const deleteModalElement = document.getElementById('deleteModal');
            const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
            const deleteForm = document.getElementById('delete-form');
            const deleteTaskTitle = document.getElementById('delete-task-title');

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.taskId;
                    const title = this.dataset.taskTitle;
                    if (deleteForm) deleteForm.action = '/tasks/' + id;
                    if (deleteTaskTitle) deleteTaskTitle.textContent = title;
                    if (deleteModal) deleteModal.show();
                });
            });

            // Success modal
            const sessionData = document.getElementById('session-data');
            if (sessionData && sessionData.dataset.taskCompleted === 'true') {
                const successModalElement = document.getElementById('successModal');
                if (successModalElement) {
                    const successModal = new bootstrap.Modal(successModalElement);
                    successModal.show();
                }
            }
        });
    </script>
@endsection
