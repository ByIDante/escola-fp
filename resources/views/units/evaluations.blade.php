@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Evaluaciones de {{ $unit->name }}
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <ul>
                @foreach($evaluations as $evaluation)
                    <li class="mb-2">
                        {{ $evaluation->name }} - {{ $evaluation->score }}
                        <a href="{{ route('evaluations.show', $evaluation) }}" class="text-blue-600 hover:underline">Ver</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
