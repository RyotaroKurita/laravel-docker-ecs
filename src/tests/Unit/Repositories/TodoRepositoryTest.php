<?php

namespace Tests\Unit\Repositories;

use App\Models\Todo;
use App\Repositories\TodoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoRepositoryTest extends TestCase
{
  use RefreshDatabase;

  protected $repo;

  protected function setUp(): void
  {
    parent::setUp();
    $this->repo = new TodoRepository();
  }

  /** @test 正常系: 全件取得 */
  public function test_it_returns_all_todos_in_desc_order()
  {
    $todo1 = Todo::factory()->create(['title' => 'First']);
    $todo2 = Todo::factory()->create(['title' => 'Second']);

    $todos = $this->repo->getAllTodos();

    $this->assertCount(2, $todos);
    $this->assertEquals($todo2->id, $todos->first()->id); // desc 順であること
  }

  /** @test 正常系: 新規作成 */
  public function test_it_creates_a_todo()
  {
    $todo = $this->repo->createTodo([
      'title' => 'New Task',
      'is_completed' => false,
    ]);

    $this->assertDatabaseHas('todos', ['title' => 'New Task']);
    $this->assertFalse($todo->is_completed);
  }

  /** @test 正常系: 更新 */
  public function test_it_updates_a_todo()
  {
    $todo = Todo::factory()->create(['is_completed' => false]);

    $updated = $this->repo->updateTodo($todo->id, ['is_completed' => true]);

    $this->assertTrue($updated->is_completed);
    $this->assertDatabaseHas('todos', ['id' => $todo->id, 'is_completed' => true]);
  }

  /** @test 正常系: 削除 */
  public function test_it_deletes_a_todo()
  {
    $todo = Todo::factory()->create();

    $result = $this->repo->deleteTodo($todo->id);

    $this->assertTrue($result);
    $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
  }

  /** @test 異常系: 存在しないIDで更新 */
  public function test_it_returns_null_when_updating_nonexistent_todo()
  {
    $updated = $this->repo->updateTodo(999, ['is_completed' => true]);
    $this->assertNull($updated);
  }

  /** @test 異常系: 存在しないIDで削除 */
  public function test_it_returns_false_when_deleting_nonexistent_todo()
  {
    $result = $this->repo->deleteTodo(999);
    $this->assertFalse($result);
  }

  /** @test 準正常系: Todoが0件のとき */
  public function test_it_returns_empty_collection_when_no_todos_exist()
  {
    $todos = $this->repo->getAllTodos();

    $this->assertCount(0, $todos);
    $this->assertTrue($todos->isEmpty());
  }

  /** @test 準正常系: すでに削除したTodoを再度削除 */
  public function test_it_returns_false_when_deleting_already_deleted_todo()
  {
    $todo = Todo::factory()->create();
    $todo->delete();

    $result = $this->repo->deleteTodo($todo->id);
    $this->assertFalse($result);
  }
}
