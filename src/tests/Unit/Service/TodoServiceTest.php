<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TodoService;
use App\Repositories\TodoRepository;
use InvalidArgumentException;

/**
 * TodoService の単体テスト（正常 / 異常 / 準正常 を網羅）
 *
 * 前提：
 * - TodoRepository はモック化して振る舞いを制御する
 * - Service は Repository の戻り値に応じてそのまま返す実装（例外は Repository から伝搬される）
 */
class TodoServiceTest extends TestCase
{
  /** @var \PHPUnit\Framework\MockObject\MockObject|TodoRepository */
  protected $repoMock;

  /** @var TodoService */
  protected $service;

  protected function setUp(): void
  {
    parent::setUp();

    // Repository を PHPUnitのモックで作る
    $this->repoMock = $this->getMockBuilder(TodoRepository::class)
      ->disableOriginalConstructor()
      ->getMock();

    // Service にモックを注入
    $this->service = new TodoService($this->repoMock);
  }

  /* ---------------------------
     * 正常系
     * --------------------------- */

  public function test_list_todos_returns_all()
  {
    $expected = [
      (object)['id' => 1, 'title' => 'Task A', 'is_completed' => false],
      (object)['id' => 2, 'title' => 'Task B', 'is_completed' => true],
    ];

    $this->repoMock->expects($this->once())
      ->method('getAllTodos')
      ->willReturn($expected);

    $result = $this->service->listTodos();

    $this->assertSame($expected, $result);
  }

  public function test_create_todo_success()
  {
    $title = 'New Task';
    $expected = (object)['id' => 1, 'title' => $title, 'is_completed' => false];

    $this->repoMock->expects($this->once())
      ->method('createTodo')
      ->with(['title' => $title, 'is_completed' => false])
      ->willReturn($expected);

    $result = $this->service->createTodo($title);

    $this->assertSame($expected, $result);
  }

  public function test_complete_todo_success()
  {
    $id = 1;
    $expected = (object)['id' => $id, 'title' => 'Task', 'is_completed' => true];

    $this->repoMock->expects($this->once())
      ->method('updateTodo')
      ->with($id, ['is_completed' => true])
      ->willReturn($expected);

    $result = $this->service->completeTodo($id);

    $this->assertSame($expected, $result);
  }

  public function test_delete_todo_success()
  {
    $id = 1;

    $this->repoMock->expects($this->once())
      ->method('deleteTodo')
      ->with($id)
      ->willReturn(true);

    $result = $this->service->deleteTodo($id);

    $this->assertTrue($result);
  }

  /* ---------------------------
     * 異常系（Repository 側で例外やエラーが発生する場合）
     * --------------------------- */

  public function test_create_todo_repository_throws_exception()
  {
    $title = ''; // 異常入力（Service自体は検証しない想定）

    // Repository が例外を投げると Service も伝搬する
    $this->repoMock->expects($this->once())
      ->method('createTodo')
      ->will($this->throwException(new InvalidArgumentException('Invalid title')));

    $this->expectException(InvalidArgumentException::class);
    $this->service->createTodo($title);
  }

  public function test_complete_todo_repository_throws_exception()
  {
    $id = 999;

    $this->repoMock->expects($this->once())
      ->method('updateTodo')
      ->with($id, ['is_completed' => true])
      ->will($this->throwException(new \RuntimeException('DB error')));

    $this->expectException(\RuntimeException::class);
    $this->service->completeTodo($id);
  }

  public function test_delete_todo_repository_throws_exception()
  {
    $id = 999;

    $this->repoMock->expects($this->once())
      ->method('deleteTodo')
      ->with($id)
      ->will($this->throwException(new \RuntimeException('DB error')));

    $this->expectException(\RuntimeException::class);
    $this->service->deleteTodo($id);
  }

  /* ---------------------------
     * 準正常系 / 境界値（エッジケース）
     * --------------------------- */

  public function test_create_todo_with_long_title()
  {
    // 255文字の長いタイトル（境界値）
    $title = str_repeat('a', 255);
    $expected = (object)['id' => 1, 'title' => $title, 'is_completed' => false];

    $this->repoMock->expects($this->once())
      ->method('createTodo')
      ->with(['title' => $title, 'is_completed' => false])
      ->willReturn($expected);

    $result = $this->service->createTodo($title);

    $this->assertSame($expected, $result);
  }

  public function test_complete_todo_not_found_returns_null()
  {
    $id = 999;

    // 存在しない id の場合、Repository が null を返す想定
    $this->repoMock->expects($this->once())
      ->method('updateTodo')
      ->with($id, ['is_completed' => true])
      ->willReturn(null);

    $result = $this->service->completeTodo($id);

    $this->assertNull($result);
  }

  public function test_delete_todo_not_found_returns_false()
  {
    $id = 999;

    $this->repoMock->expects($this->once())
      ->method('deleteTodo')
      ->with($id)
      ->willReturn(false);

    $result = $this->service->deleteTodo($id);

    $this->assertFalse($result);
  }

  public function test_complete_todo_with_negative_id()
  {
    $id = -1;

    // 負数IDは通常ありえないが、Repositoryがnullを返す想定で検証
    $this->repoMock->expects($this->once())
      ->method('updateTodo')
      ->with($id, ['is_completed' => true])
      ->willReturn(null);

    $result = $this->service->completeTodo($id);

    $this->assertNull($result);
  }
}
