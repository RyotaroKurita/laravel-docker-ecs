<!-- resources/views/todos/index.blade.php -->
@extends('todos.layout')

@section('content')
  <!-- 新規登録フォーム -->
  <form action="{{ route('todos.store') }}" method="POST" class="flex mb-6">
    @csrf
    <input type="text" name="title" placeholder="やることを入力" class="flex-grow border rounded-l px-3 py-2 focus:outline-none" required>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r hover:bg-blue-600">
      追加
    </button>
  </form>

  <!-- バリデーションエラー -->
  @if ($errors->any())
    <div class="mb-4 text-red-600">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Todo一覧 -->
  <ul>
    @forelse ($todos as $todo)
      <li class="flex items-center justify-between border-b py-2">
        <div>
          <span class="{{ $todo->is_completed ? 'line-through text-gray-500' : '' }}">
            {{ $todo->title }}
          </span>
        </div>
        <div class="flex space-x-2">
          @if (!$todo->is_completed)
            <form action="{{ route('todos.complete', $todo->id) }}" method="POST">
              @csrf
              @method('PATCH')
              <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                完了
              </button>
            </form>
          @endif
          <form action="{{ route('todos.destroy', $todo->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
              削除
            </button>
          </form>
        </div>
      </li>
    @empty
      <li class="text-gray-500 text-center py-4">まだタスクがありません</li>
    @endforelse
  </ul>
@endsection
