<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Forgot Password?</h1>
        <p class="mt-2 text-sm text-gray-600">No worries! Enter your email and we'll send you a reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a class="text-sm text-purple-600 hover:text-purple-700 font-medium" href="{{ route('login') }}">
                &larr; {{ __('Back to Sign In') }}
            </a>
        </div>
    </form>
</x-guest-layout>
