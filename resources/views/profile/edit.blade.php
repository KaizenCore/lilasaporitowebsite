<x-public-layout>
    <x-slot name="title">Profile - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Profile Settings</h1>
            <p class="text-purple-100">Manage your account information</p>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="py-12 px-4">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Delete Account -->
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>
</x-public-layout>
