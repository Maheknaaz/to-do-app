<?php
$file = 'tasks.json';

// Load existing tasks
$tasks = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "add" && isset($_POST['task'])) {
        $tasks[] = ["id" => uniqid(), "text" => $_POST['task'], "completed" => false];
    } elseif ($action == "edit" && isset($_POST['id'], $_POST['task'])) {
        foreach ($tasks as &$task) {
            if ($task["id"] == $_POST['id']) {
                $task["text"] = $_POST['task'];
            }
        }
    } elseif ($action == "complete" && isset($_POST['id'])) {
        foreach ($tasks as &$task) {
            if ($task["id"] == $_POST['id']) {
                $task["completed"] = !$task["completed"];
            }
        }
    } elseif ($action == "delete" && isset($_POST['id'])) {
        $tasks = array_values(array_filter($tasks, function($task) {
            return $task["id"] != $_POST['id'];
        }));
    }

    if (!file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT))) {
        echo "Error saving tasks";
        exit;
    }

    echo "Success";
    exit;
}

// Display tasks
foreach ($tasks as $task) {
    $completedClass = $task["completed"] ? "completed" : "";
    echo "<li class='task $completedClass' data-id='{$task['id']}'>
        <span class='task-text'>{$task['text']}</span>
        <button class='btn complete'>✔</button>
        <button class='btn edit'>✏</button>
        <button class='btn delete'>❌</button>
    </li>";
}
?>
