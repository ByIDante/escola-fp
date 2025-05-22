<x-guest-layout>
    <!-- Mensaje de éxito tras registro -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Errores generales (no de campo) -->
    @if ($errors->has('register'))
        <div class="mb-4 text-sm text-red-600 dark:text-red-400">
            {{ $errors->first('register') }}
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="mb-4 text-sm text-red-600 dark:text-red-400">
            {{ $errors->first('general') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nombre completo')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Rol')" />
            <select id="role" name="role" class="block mt-1 w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required onchange="mostrarCampos()">
                <option value="">Selecciona un rol</option>
                <option value="STUDENT" {{ old('role') == 'STUDENT' ? 'selected' : '' }}>Estudiante</option>
                <option value="TEACHER" {{ old('role') == 'TEACHER' ? 'selected' : '' }}>Profesor</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Campos estudiante -->
        <div id="student_fields" class="mt-4" style="display: none;">
            <x-input-label for="student_data_first_name" :value="__('Nombre de estudiante')" />
            <x-text-input id="student_data_first_name" class="block mt-1 w-full" type="text" name="student_data[first_name]" :value="old('student_data.first_name')" />
            <x-input-error :messages="$errors->get('student_data.first_name')" class="mt-2" />

            <x-input-label for="student_data_last_name" :value="__('Apellidos de estudiante')" class="mt-2" />
            <x-text-input id="student_data_last_name" class="block mt-1 w-full" type="text" name="student_data[last_name]" :value="old('student_data.last_name')" />
            <x-input-error :messages="$errors->get('student_data.last_name')" class="mt-2" />
        </div>

        <!-- Campos profesor -->
        <div id="teacher_fields" class="mt-4" style="display: none;">
            <x-input-label for="teacher_data_first_name" :value="__('Nombre de profesor')" />
            <x-text-input id="teacher_data_first_name" class="block mt-1 w-full" type="text" name="teacher_data[first_name]" :value="old('teacher_data.first_name')" />
            <x-input-error :messages="$errors->get('teacher_data.first_name')" class="mt-2" />

            <x-input-label for="teacher_data_last_name" :value="__('Apellidos de profesor')" class="mt-2" />
            <x-text-input id="teacher_data_last_name" class="block mt-1 w-full" type="text" name="teacher_data[last_name]" :value="old('teacher_data.last_name')" />
            <x-input-error :messages="$errors->get('teacher_data.last_name')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        function mostrarCampos() {
            var role = document.getElementById('role').value;
            document.getElementById('student_fields').style.display = (role === 'STUDENT') ? 'block' : 'none';
            document.getElementById('teacher_fields').style.display = (role === 'TEACHER') ? 'block' : 'none';
        }
        document.addEventListener('DOMContentLoaded', function() {
            mostrarCampos();
        });
    </script>
</x-guest-layout>
