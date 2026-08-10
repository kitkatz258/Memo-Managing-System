<x-guest-layout>
    <div class="login-page min-h-screen bg-cover bg-center relative"
        style="background-image:url('{{ asset('images/login-bg.jpg') }}');">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/30"></div>

        <!-- Login Card -->
        <div class="relative flex items-center justify-center min-h-screen px-4">

            <div class="w-full max-w-md bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-8">

                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <i class="ri-file-text-line text-6xl text-indigo-600"></i>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-center text-gray-800">
                    Memo Management System
                </h1>

                <p class="text-center text-gray-500 mt-2 mb-8">
                    Multi-Line Memorandum Repository
                </p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username -->
                    <div class="mb-5">
                        <label class="block mb-2 font-semibold text-gray-700">
                            Username
                        </label>

                        <input
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full h-11 px-4 rounded-lg border border-gray-300 !bg-white text-gray-800
                                placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500
                                transition"
                        >

                        @error('username')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label class="block mb-2 font-semibold text-gray-700">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full h-11 px-4 rounded-lg border border-gray-300 !bg-white text-gray-800
                                placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500
                                transition"
                        >

                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Login -->
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                            text-white py-3 rounded-lg font-semibold transition">
                        Login
                    </button>

                </form>

            </div>

        </div>

    </div>
</x-guest-layout>