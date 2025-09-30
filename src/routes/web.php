<?php

use App\Http\Controllers\TodoController;

use Illuminate\Support\Facades\Route;


Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
Route::patch('/todos/{id}/complete', [TodoController::class, 'complete'])->name('todos.complete');
Route::delete('/todos/{id}', [TodoController::class, 'destroy'])->name('todos.destroy');
