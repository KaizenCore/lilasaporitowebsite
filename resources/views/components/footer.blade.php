<footer class="bg-gray-900 text-white py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">FrizzBoss</h3>
                <p class="text-gray-400">Inspiring creativity, one class at a time.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Explore</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('classes.index') }}" class="hover:text-purple-400 transition">Classes</a></li>
                    <li><a href="{{ route('store.index') }}" class="hover:text-purple-400 transition">Store</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-purple-400 transition">About Lila</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Account</h4>
                <ul class="space-y-2 text-gray-400">
                    @auth
                        <li><a href="{{ route('bookings.index') }}" class="hover:text-purple-400 transition">My Bookings</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-purple-400 transition">Dashboard</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-purple-400 transition">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-purple-400 transition">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-purple-400 transition">Sign Up</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Contact</h4>
                <p class="text-gray-400">Questions? Reach out to us and we'll get back to you soon!</p>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} FrizzBoss. All rights reserved.</p>
        </div>
    </div>
</footer>
