@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Profesores
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <a href="{{ route('teachers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Nuevo profesor</a>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                            <a href="{{ route('teachers.show', $teacher) }}" class="text-blue-600 hover:underline">Ver</a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="text-yellow-600 hover:underline">Editar</a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('¿Seguro que deseas eliminar este profesor?')">Eliminar</button>
                            </form>
                            <a href="{{ route('teachers.evaluations', $teacher) }}" class="text-gray-600 hover:underline">Evaluaciones</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
