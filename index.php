<?php
// Simple OOP Todo class
class Todo {
    private $todos = [];
    private $file = 'todos.json';

    public function __construct() {
        if (file_exists($this->file)) {
            $this->todos = json_decode(file_get_contents($this->file), true) ?? [];
        }
    }

    public function getAll() {
        return $this->todos;
    }

    public function add($task) {
        $id = uniqid();
        $this->todos[$id] = ['id' => $id, 'task' => htmlspecialchars($task), 'done' => false];
        $this->save();
    }

    public function delete($id) {
        unset($this->todos[$id]);
        $this->save();
    }

    public function toggle($id) {
        if (isset($this->todos[$id])) {
            $this->todos[$id]['done'] = !$this->todos[$id]['done'];
            $this->save();
        }
    }

    public function edit($id, $task) {
        if (isset($this->todos[$id])) {
            $this->todos[$id]['task'] = htmlspecialchars($task);
            $this->save();
        }
    }

    private function save() {
        file_put_contents($this->file, json_encode($this->todos));
    }
}

$todo = new Todo();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' && !empty($_POST['task'])) {
            $todo->add($_POST['task']);
        }
        if ($_POST['action'] === 'edit' && !empty($_POST['task']) && !empty($_POST['id'])) {
            $todo->edit($_POST['id'], $_POST['task']);
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle GET actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && !empty($_GET['id'])) {
        $todo->delete($_GET['id']);
    }
    if ($_GET['action'] === 'toggle' && !empty($_GET['id'])) {
        $todo->toggle($_GET['id']);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$todos = $todo->getAll();
$editId = $_GET['edit'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TodoList App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>📝 TodoList App</h2>
        <!-- Add/Edit Form -->
        <form method="POST" id="todoForm" autocomplete="off">
            <input type="hidden" name="action" value="<?= $editId ? 'edit' : 'add' ?>">
            <?php if ($editId && isset($todos[$editId])): ?>
                <input type="hidden" name="id" value="<?= $editId ?>">
                <textarea name="task" id="taskInput" required autofocus style="resize:vertical;min-height:40px;"><?= htmlspecialchars($todos[$editId]['task']) ?></textarea>
                <button type="submit">Update</button>
                <button type="button" class="btn" onclick="window.location='index.php'">Cancel</button>
            <?php else: ?>
                <textarea name="task" id="taskInput" placeholder="Add new task..." required autofocus style="resize:vertical;min-height:40px;"></textarea>
                <button type="submit">Add</button>
            <?php endif; ?>
        </form>
        <!-- Todo List -->
        <ul id="todoList">
            <?php foreach ($todos as $item): ?>
            <li class="todo-item<?= $item['done'] ? ' done' : '' ?>" data-id="<?= $item['id'] ?>">
                <form method="GET" style="display:inline;margin-right:10px;" class="toggle-form">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <input type="checkbox" onchange="this.form.submit()" <?= $item['done'] ? 'checked' : '' ?> title="Mark as <?= $item['done'] ? 'Undone' : 'Done' ?>">
                </form>
                <span class="todo-task">
                <?= nl2br($item['task']) ?>
                (<?= $item['done'] ? 'Selesai' : 'Belum' ?>)
                </span>
                <div class="actions">
                <a href="?edit=<?= $item['id'] ?>" class="btn-action" title="Edit">✏️</a>
                <form method="GET" style="display:inline;" onsubmit="return confirm('Delete this task?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button class="btn-action" title="Delete">🗑️</button>
                </form>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script src="js/script.js"></script>
</body>
</html>