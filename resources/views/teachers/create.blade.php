@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Nuevo profesor
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('teachers.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Nombre</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Email</label>
                    <input type="email" name="email" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-200">Contraseña</label>
                    <input type="password" name="password" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Crear</button>
            </form>
        </div>
    </div>
</div>
@endsection
