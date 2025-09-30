<?php

namespace App\Http\Controllers;

use App\Services\TodoService;
use App\Http\Requests\StoreTodoRequest;

use Illuminate\Http\Request;

class TodoController extends Controller
{
  protected $service;

  public function __construct(TodoService $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    $todos = $this->service->listTodos();
    return view('todos.index', ['todos' => $todos]);
  }

  public function store(StoreTodoRequest $request)
  {
    $this->service->createTodo($request->title);
    return redirect()->route('todos.index');
  }

  public function complete($id)
  {
    $this->service->completeTodo($id);
    return redirect()->route('todos.index');
  }

  public function destroy($id)
  {
    $this->service->deleteTodo($id);
    return redirect()->route('todos.index');
  }
}
