@extends('layouts.admin')

@section('header', 'Panel de control')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold">Resumen</h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-100 p-4 rounded-lg grow-on-hover">
                        <div class="font-bold text-3xl text-blue-600">{{ $totalUsers }}</div>
                        <div class="text-gray-700">Usuarios registrados</div>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg grow-on-hover">
                        <div class="font-bold text-3xl text-green-600">{{ $totalProperties }}</div>
                        <div class="text-gray-700">Propiedades</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold">Acciones rápidas</h2>
            </div>
            <div class="card-body">
                <div class="space-y-2">
                    <a href="{{ route('admin.users.index') }}" class="btn-primary w-full">
                        Gestionar usuarios
                    </a>
                    <a href="{{ route('admin.properties.index') }}" class="btn-secondary w-full">
                        Gestionar propiedades
                    </a>
                    <a href="{{ route('admin.features.index') }}"
                        class="btn w-full bg-purple-600 text-white hover:bg-purple-700 focus:ring-purple-500">
                        Gestionar características
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold">Propiedades recientes</h2>
            </div>
            <div class="card-body">
                @if($recentProperties->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentProperties as $property)
                                <div class="flex items-center border-b pb-3">
                                    <div class="w-16 h-16 mr-4">
                                        @if($property->featured_image)
                                                        @php
                                                            // Comprobar si la imagen es una URL externa o un path local
                                                            $imagePath = $property->featured_image;
                                                            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                                // Si no es una URL, comprobar si tiene el prefijo /storage/ o storage/
                                                                if (!str_starts_with($imagePath, '/storage/') && !str_starts_with($imagePath, 'storage/')) {
                                                                    $imagePath = 'storage/' . $imagePath;
                                                                }
                                                                $imagePath = asset($imagePath);
                                                            }
                                                        @endphp
                                                        <img src="{{ $imagePath }}" alt="{{ $property->title }}"
                                                            class="w-full h-full object-cover rounded">
                                        @elseif($property->images && $property->images->count() > 0)
                                                        @php
                                                            // Obtener la imagen principal o la primera disponible
                                                            $image = $property->images->firstWhere('is_main', true) ?? $property->images->first();
                                                            $imagePath = $image->path;

                                                            // Comprobar si la imagen es una URL externa o un path local
                                                            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                                                                // Si no es una URL, comprobar si tiene el prefijo /storage/ o storage/
                                                                if (!str_starts_with($imagePath, '/storage/') && !str_starts_with($imagePath, 'storage/')) {
                                                                    $imagePath = 'storage/' . $imagePath;
                                                                }
                                                                $imagePath = asset($imagePath);
                                                            }
                                                        @endphp
                                                        <img src="{{ $imagePath }}" alt="{{ $property->title }}"
                                                            class="w-full h-full object-cover rounded">
                                        @else
                                            <div class="w-full h-full bg-gray-300 flex items-center justify-center rounded">
                                                <span class="text-xs text-gray-500">Sin imagen</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.properties.show', $property) }}"
                                            class="font-medium hover:text-blue-600">{{ $property->title }}</a>
                                        <div class="text-sm text-gray-600">{{ number_format($property->price) }}€ -
                                            {{ $property->city }}</div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">No hay propiedades recientes.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold">Usuarios recientes</h2>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center border-b pb-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-4">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="font-medium hover:text-blue-600">{{ $user->name }}</a>
                                    <div class="text-sm text-gray-600">{{ $user->email }} - {{ ucfirst($user->role) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">No hay usuarios recientes.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
