<?php

namespace App\Repositories;

use App\Models\Todo;

class TodoRepository
{
  public function getAllTodos()
  {
    return Todo::orderBy("id", "desc")->get();
  }

  public function createTodo(array $data)
  {
    return Todo::create($data);
  }

  public function updateTodo(int $id, array $data)
  {
    $todo = Todo::find($id);
    if (!$todo) {
      return null;
    }

    $todo->update($data);
    return $todo;
  }

  public function deleteTodo(int $id)
  {
    $todo = Todo::find($id);
    if (!$todo) {
      return false;
    }

    return $todo->delete();
  }
}
