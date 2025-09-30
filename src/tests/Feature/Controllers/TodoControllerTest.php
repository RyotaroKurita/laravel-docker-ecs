<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\TodoController;
use App\Services\TodoService;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class TodoControllerTest extends TestCase
{
  use RefreshDatabase;

  protected $serviceMock;

  protected function setUp(): void
  {
    parent::setUp();
    $this->serviceMock = Mockery::mock(TodoService::class);
    $this->app->instance(TodoService::class, $this->serviceMock);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }

  /** @test 正常系: indexでTodo一覧が表示される */
  public function  test_index_displays_todos()
  {
    $todo1 = new Todo(['title' => 'First', 'is_completed' => false]);
    $todo1->id = 1;

    $todo2 = new Todo(['title' => 'Second', 'is_completed' => true]);
    $todo2->id = 2;

    $todos = collect([$todo1, $todo2]);

    $this->serviceMock
      ->shouldReceive('listTodos')
      ->once()
      ->andReturn($todos);

    $response = $this->get(route('todos.index'));

    $response->assertStatus(200);
    $response->assertViewIs('todos.index');
    $response->assertViewHas('todos', $todos);
  }

  /** @test 正常系: storeで新規Todo作成後リダイレクト */
  public function  test_store_creates_todo_and_redirects()
  {
    $this->serviceMock
      ->shouldReceive('createTodo')
      ->once()
      ->with('New Task');

    $response = $this->post(route('todos.store'), ['title' => 'New Task']);

    $response->assertRedirect(route('todos.index'));
  }

  /** @test 異常系: storeでtitle未入力はバリデーションエラー */
  public function  test_store_fails_validation_without_title()
  {
    $response = $this->post(route('todos.store'), ['title' => '']);

    $response->assertSessionHasErrors('title');
  }

  /** @test 正常系: completeで完了フラグを立てリダイレクト */
  public function  test_complete_marks_todo_as_completed_and_redirects()
  {
    $this->serviceMock
      ->shouldReceive('completeTodo')
      ->once()
      ->with(1);

    $response = $this->patch(route('todos.complete', 1));

    $response->assertRedirect(route('todos.index'));
  }

  /** @test 正常系: destroyでTodo削除後リダイレクト */
  public function  test_destroy_deletes_todo_and_redirects()
  {
    $this->serviceMock
      ->shouldReceive('deleteTodo')
      ->once()
      ->with(1);

    $response = $this->delete(route('todos.destroy', 1));

    $response->assertRedirect(route('todos.index'));
  }

  /** @test 準正常系: 完了/削除対象のIDが存在しない場合もリダイレクトする */
  public function test_complete_or_destroy_nonexistent_todo_redirects_gracefully()
  {
    $this->serviceMock->shouldReceive('completeTodo')->with(999)->once();
    $this->serviceMock->shouldReceive('deleteTodo')->with(999)->once();

    $response1 = $this->patch(route('todos.complete', 999));
    $response2 = $this->delete(route('todos.destroy', 999));

    $response1->assertRedirect(route('todos.index'));
    $response2->assertRedirect(route('todos.index'));
  }
}
