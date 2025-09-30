<!-- resources/views/todos/layout.blade.php -->
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>Todoアプリ</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Todoアプリ</h1>
    <div class="bg-white shadow rounded-lg p-6">
      @yield('content')
    </div>
  </div>
</body>

</html>
