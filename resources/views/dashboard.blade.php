<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; margin:0; background: #f6f8fa; }
        .container { display:flex; min-height:100vh; align-items:center; justify-content:center; }
        .card { background:white; max-width:720px; width:100%; padding:28px; border-radius:12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        h1 { margin:0; font-size:22px; color:#0f172a }
        p { color:#475569; margin-top:6px }
        .actions { margin-top:18px; display:flex; gap:12px; }
        .btn { background: #111827; color:white; padding:10px 16px; border-radius:8px; text-decoration:none; border:none; cursor:pointer }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Bem-vindo(a), {{ auth()->user()->name }}</h1>
        <p>Você está autenticado com o email: {{ auth()->user()->email }}</p>

        <div style="margin-top:18px;display:flex;gap:12px;align-items:center;">
            @if (isset($tasks) && $tasks->count() > 0)
                <button id="clear-all-btn" class="btn" style="background:#ef4444;border:none;">Excluir todas as tarefas</button>

                {{-- Hidden form to perform DELETE against tasks.clear --}}
                <form id="clear-all-form" method="POST" action="{{ route('tasks.clear') }}" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="btn" type="submit">Sair</button>
            </form>
        </div>

        <hr style="margin-top:18px;border:none;border-top:1px solid #e6e9ef;">

        {{-- Flash message and errors --}}
        @if (session('success'))
            <div style="margin-top:12px;padding:10px;border-radius:8px;background:#d1fae5;color:#065f46;font-weight:600;">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div style="margin-top:12px;padding:10px;border-radius:8px;background:#fee2e2;color:#991b1b;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Task Form --}}
        <div style="margin-top:18px">
            <form method="POST" action="{{ url('/tasks') }}">
                @csrf
                <label for="title" style="display:block;font-size:13px;color:#334155;">Nova Tarefa</label>
                <input type="text" name="title" id="title" placeholder="Título da tarefa" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;margin-top:6px;">

                <label for="description" style="display:block;font-size:13px;color:#334155;margin-top:8px;">Descrição</label>
                <textarea name="description" id="description" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;margin-top:6px;"></textarea>

                <div style="display:flex;gap:8px;margin-top:8px;">
                    <div style="flex:1;">
                        <label for="priority" style="display:block;font-size:13px;color:#334155;">Prioridade</label>
                        <select name="priority" id="priority" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;margin-top:6px;">
                            <option value="1">Baixa</option>
                            <option value="2">Média</option>
                            <option value="3">Alta</option>
                        </select>
                    </div>

                    <div style="flex:1;">
                        <label for="due_date" style="display:block;font-size:13px;color:#334155;">Vence em</label>
                        <input type="date" name="due_date" id="due_date" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;margin-top:6px;">
                    </div>
                </div>

                <button type="submit" class="btn" style="margin-top:12px;">Adicionar tarefa</button>
            </form>
        </div>

        {{-- List tasks --}}
        <div style="margin-top:18px;">
            <h3 style="margin:0 0 8px 0;color:#0f172a;font-size:18px;">Minhas tarefas</h3>

            @if (isset($tasks) && $tasks->count() > 0)
                <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                    @foreach ($tasks as $task)
                        @php $taskData = $task->only(["id","title","description","priority","due_date"]) @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;border-radius:8px;border:1px solid #e6e9ef;background:#fff;">
                            <div style="display:flex;gap:12px;align-items:center;">
                                <form method="POST" action="{{ url('/tasks/'.$task->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_completed" value="{{ $task->is_completed ? 0 : 1 }}">
                                    <button type="submit" style="background:none;border:none;cursor:pointer;margin-right:8px;font-size:18px;">
                                        {!! $task->is_completed ? '&#x2705;' : '&#x2B1C;' !!}
                                    </button>
                                </form>

                                <div>
                                    <div style="font-weight:700;color:#0f172a;">{{ $task->title }}</div>
                                    <div style="font-size:13px;color:#475569;">{{ $task->description }}</div>
                                    @php
                                        $days = $task->days_remaining; 
                                        $badgeBg = $days === null ? '#e5e7eb' : ($days < 0 ? '#ef4444' : ($days === 0 ? '#fb923c' : ($days <= 7 ? '#f59e0b' : '#10b981')));
                                        if ($days === null) {
                                            $badgeText = '-';
                                        } elseif ($days < 0) {
                                            $badgeText = 'Vencida há '.abs($days).' dias';
                                        } elseif ($days === 0) {
                                            $badgeText = 'Hoje';
                                        } else {
                                            $badgeText = 'Faltam '.$days.' dias';
                                        }
                                    @endphp
                                    <div style="font-size:12px;color:#6b7280;margin-top:6px;display:flex;align-items:center;gap:8px;">
                                        <span>Prioridade: {{ $task->priority }} • {{ $task->due_status }}</span>
                                        <span style="background:{{ $badgeBg }};color:#0f172a;padding:4px 8px;border-radius:999px;font-size:12px;">{{ $badgeText }}</span>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex;gap:8px;align-items:center;">
                                <form id="delete-form-{{ $task->id }}" method="POST" action="{{ url('/tasks/'.$task->id) }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button class="btn delete-btn" type="button" 
                                    data-task-id="{{ $task->id }}"
                                    data-task-title="{{ $task->title }}"
                                    style="background:#ef4444;border:none;">Remover</button>
                                
                                <!-- Edit button -->
                                <button class="btn edit-btn" type="button" 
                                    data-task='@json($taskData)'
                                    style="background:#1d4ed8;border:none;">Editar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="color:#475569;margin-top:8px;">Nenhuma tarefa encontrada. Adicione sua primeira tarefa!</div>
            @endif
        </div>

        {{-- Modal for clear all confirmation --}}
        <div id="clear-modal" style="display:none;position:fixed;inset:0;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
            <div style="background:#fff;max-width:520px;margin:0 auto;padding:20px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);">
                <h2 style="margin:0 0 8px 0;color:#0f172a;">Confirmar exclusão</h2>
                <p style="color:#475569;margin:0 0 18px 0;">Você tem certeza que deseja excluir <strong>todas</strong> as suas tarefas? Esta ação não pode ser desfeita.</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button id="clear-modal-cancel" class="btn" type="button" style="background:#e5e7eb;color:#111827;">Cancelar</button>
                    <button id="clear-modal-confirm" class="btn" type="button" style="background:#ef4444;border:none;">Sim, excluir todas</button>
                </div>
            </div>
        </div>
        
        {{-- Success modal for task completion --}}
        <div id="success-modal" style="display:none;position:fixed;inset:0;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
            <div style="background:#fff;max-width:520px;margin:0 auto;padding:20px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);">
                <h2 style="margin:0 0 8px 0;color:#0f172a;">Sucesso!</h2>
                <p style="color:#475569;margin:0 0 18px 0;">Tarefa concluída com sucesso! 🎉</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button id="success-modal-close" class="btn" type="button" style="background:#10b981;border:none;">Fechar</button>
                </div>
            </div>
        </div>
        
        {{-- Modal for delete task confirmation --}}
        <div id="delete-modal" style="display:none;position:fixed;inset:0;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
            <div style="background:#fff;max-width:520px;margin:0 auto;padding:20px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);">
                <h2 style="margin:0 0 8px 0;color:#0f172a;">Confirmar exclusão</h2>
                <p id="delete-modal-text" style="color:#475569;margin:0 0 18px 0;">Você tem certeza que deseja excluir a tarefa "<strong id="delete-task-title"></strong>"? Esta ação não pode ser desfeita.</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button id="delete-modal-cancel" class="btn" type="button" style="background:#e5e7eb;color:#111827;">Cancelar</button>
                    <button id="delete-modal-confirm" class="btn" type="button" style="background:#ef4444;border:none;">Sim, excluir</button>
                </div>
            </div>
        </div>
        
        {{-- Edit modal --}}
        <div id="edit-modal" style="display:none;position:fixed;inset:0;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
            <div style="background:#fff;max-width:720px;min-width:320px;margin:0 auto;padding:20px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);">
                <h2 style="margin:0 0 8px 0;color:#0f172a;">Editar tarefa</h2>
                <form id="edit-form" method="POST" action="" style="display:flex;flex-direction:column;gap:8px;">
                    @csrf
                    @method('PATCH')
                    <label for="edit-title" style="display:block;font-size:13px;color:#334155;">Título</label>
                    <input id="edit-title" name="title" type="text" style="padding:10px;border-radius:8px;border:1px solid #e6e9ef;">

                    <label for="edit-description" style="display:block;font-size:13px;color:#334155;">Descrição</label>
                    <textarea id="edit-description" name="description" rows="3" style="padding:10px;border-radius:8px;border:1px solid #e6e9ef;"></textarea>

                    <div style="display:flex;gap:8px;">
                        <div style="flex:1;">
                            <label for="edit-priority" style="display:block;font-size:13px;color:#334155;">Prioridade</label>
                            <select id="edit-priority" name="priority" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;">
                                <option value="1">Baixa</option>
                                <option value="2">Média</option>
                                <option value="3">Alta</option>
                            </select>
                        </div>

                        <div style="flex:1;">
                            <label for="edit-due_date" style="display:block;font-size:13px;color:#334155;">Vence em</label>
                            <input id="edit-due_date" name="due_date" type="date" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e9ef;">
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
                        <button id="edit-cancel" type="button" class="btn" style="background:#e5e7eb;color:#111827;">Cancelar</button>
                        <button id="edit-save" type="submit" class="btn" style="background:#10b981;border:none;">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var openBtn = document.getElementById('clear-all-btn');
        var modal = document.getElementById('clear-modal');
        var cancelBtn = document.getElementById('clear-modal-cancel');
        var confirmBtn = document.getElementById('clear-modal-confirm');
        var form = document.getElementById('clear-all-form');

        if (openBtn && modal) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'flex';
            });
        }

        if (cancelBtn && modal) {
            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        if (confirmBtn && form) {
            confirmBtn.addEventListener('click', function() {
                form.submit();
            });
        }
        
        // Success modal for task completion
        var successModal = document.getElementById('success-modal');
        var successCloseBtn = document.getElementById('success-modal-close');

        if (successCloseBtn && successModal) {
            successCloseBtn.addEventListener('click', function() {
                successModal.style.display = 'none';
            });
        }

        // Show success modal if task was just completed
        @if (session('task_completed'))
            if (successModal) {
                successModal.style.display = 'flex';
            }
        @endif
        
        // Delete modal logic
        var deleteModal = document.getElementById('delete-modal');
        var deleteModalText = document.getElementById('delete-modal-text');
        var deleteTaskTitle = document.getElementById('delete-task-title');
        var deleteCancelBtn = document.getElementById('delete-modal-cancel');
        var deleteConfirmBtn = document.getElementById('delete-modal-confirm');
        var currentDeleteForm = null;

        function openDeleteModal(taskId, taskTitle) {
            deleteTaskTitle.textContent = taskTitle;
            currentDeleteForm = document.getElementById('delete-form-' + taskId);
            deleteModal.style.display = 'flex';
        }

        function closeDeleteModal() {
            deleteModal.style.display = 'none';
            currentDeleteForm = null;
        }

        if (deleteCancelBtn && deleteModal) {
            deleteCancelBtn.addEventListener('click', function() {
                closeDeleteModal();
            });
        }

        if (deleteConfirmBtn && currentDeleteForm) {
            deleteConfirmBtn.addEventListener('click', function() {
                if (currentDeleteForm) {
                    currentDeleteForm.submit();
                }
            });
        }

        // Close delete modal by clicking outside
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });

        // Attach event listeners to all delete buttons
        document.querySelectorAll('.delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var taskId = btn.getAttribute('data-task-id');
                var taskTitle = btn.getAttribute('data-task-title');
                openDeleteModal(taskId, taskTitle);
            });
        });
        
        // Edit modal logic
        var editModal = document.getElementById('edit-modal');
        var editForm = document.getElementById('edit-form');
        var editCancel = document.getElementById('edit-cancel');
        var editTitle = document.getElementById('edit-title');
        var editDescription = document.getElementById('edit-description');
        var editPriority = document.getElementById('edit-priority');
        var editDueDate = document.getElementById('edit-due_date');

        function openEditModal(taskData) {
            editForm.action = '/tasks/' + taskData.id;
            editTitle.value = taskData.title || '';
            editDescription.value = taskData.description || '';
            editPriority.value = taskData.priority || '1';
            editDueDate.value = taskData.due_date || '';
            editModal.style.display = 'flex';
            editTitle.focus();
        }

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        editCancel.addEventListener('click', function() {
            closeEditModal();
        });
        
        // Close modal by clicking outside the content
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Attach event listeners to all edit buttons
        document.querySelectorAll('.edit-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var payload = btn.getAttribute('data-task');
                var taskData = {};
                try {
                    taskData = JSON.parse(payload);
                } catch (err) {
                    console.warn('Invalid task data', err);
                }
                // normalize date format; ensure due_date is 'Y-m-d' if present
                if (taskData.due_date && taskData.due_date.indexOf(' ') !== -1) {
                    taskData.due_date = taskData.due_date.split(' ')[0];
                }
                openEditModal(taskData);
            });
        });
    });
    </script>
</html>
