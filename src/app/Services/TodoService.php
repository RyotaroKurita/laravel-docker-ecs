<?php

namespace App\Services;

use App\Repositories\TodoRepository;

class TodoService {
  protected $repo;

  public function __construct(TodoRepository $repo) {
              $this->repo = $repo;
  }

  public function listTodos() {
         return $this->repo->getAllTodos();
  }

  public function createTodo(string $title) {
    return $this->repo->createTodo(   ['title' => $title, 'is_completed' => false]  );
  }

  public function completeTodo(int $id) {


    return $this->repo->updateTodo(
      $id, ['is_completed' => true]
    );
  }

  public function deleteTodo(int $id) {
    
         return $this->repo->deleteTodo($id);
  }
}
