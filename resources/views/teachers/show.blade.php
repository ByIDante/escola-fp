@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Detalle profesor
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <p><strong>Nombre:</strong> {{ $teacher->user->name }}</p>
            <p><strong>Email:</strong> {{ $teacher->user->email }}</p>
            <a href="{{ route('teachers.evaluations', $teacher) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 mt-4 inline-block">Ver evaluaciones</a>
        </div>
    </div>
</div>
@endsection
