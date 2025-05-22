<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel principal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">Bienvenido, {{ Auth::user()->name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('students.index') }}" class="block bg-blue-600 text-white rounded-lg p-6 text-center hover:bg-blue-700 transition">Estudiantes</a>
                        <a href="{{ route('teachers.index') }}" class="block bg-green-600 text-white rounded-lg p-6 text-center hover:bg-green-700 transition">Profesores</a>
                        <a href="{{ route('modules.index') }}" class="block bg-purple-600 text-white rounded-lg p-6 text-center hover:bg-purple-700 transition">Módulos</a>
                        <a href="{{ route('units.index') }}" class="block bg-yellow-600 text-white rounded-lg p-6 text-center hover:bg-yellow-700 transition">Unidades</a>
                        <a href="{{ route('evaluations.index') }}" class="block bg-pink-600 text-white rounded-lg p-6 text-center hover:bg-pink-700 transition">Evaluaciones</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
