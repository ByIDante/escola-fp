@extends('layouts.admin')

@section('header', 'Editar usuario')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-6">Editar usuario: {{ $user->name }}</h2>

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block mb-2 font-medium">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block mb-2 font-medium">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block mb-2 font-medium">Rol</label>
                    <select name="role" id="role" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="USER" {{ old('role', $user->role) == 'USER' ? 'selected' : '' }}>Usuario</option>
                        <option value="ADMIN" {{ old('role', $user->role) == 'ADMIN' ? 'selected' : '' }}>Administrador
                        </option>
                    </select>
                    @error('role')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded text-gray-800">
                    Cancelar
                </a>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white">
                    Guardar cambios
                </button>
            </div>
        </form>

        <div class="mt-12 border-t pt-6">
            <h3 class="text-lg font-bold mb-4">Propiedades del usuario</h3>

            @if($user->properties->count() > 0)
                <div class="overflow-hidden border-b border-gray-200">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->properties as $property)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $property->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($property->price) }} €</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($property->status == 'available') bg-green-100 text-green-800
                                                        @elseif($property->status == 'reserved') bg-yellow-100 text-yellow-800
                                                        @elseif($property->status == 'sold') bg-red-100 text-red-800
                                                        @elseif($property->status == 'rented') bg-blue-100 text-blue-800
                                                        @endif">
                                            @if($property->status == 'available')
                                                Disponible
                                            @elseif($property->status == 'reserved')
                                                Reservada
                                            @elseif($property->status == 'sold')
                                                Vendida
                                            @elseif($property->status == 'rented')
                                                Alquilada
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.properties.edit', $property) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Este usuario no tiene propiedades.</p>
            @endif
        </div>
    </div>
@endsection
