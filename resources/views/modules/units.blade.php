@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Unidades de {{ $module->name }}
    </h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <ul>
                @foreach($units as $unit)
                    <li class="mb-2">
                        {{ $unit->name }}
                        <a href="{{ route('units.show', $unit) }}" class="text-blue-600 hover:underline">Ver</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
