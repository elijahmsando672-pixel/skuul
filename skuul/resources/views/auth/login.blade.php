<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
        @csrf

        <div class="rounded-md bg-skuul-50 px-4 py-5 shadow sm:px-10">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <a href="{{ route('register') }}" class="font-medium text-skuul-600 hover:text-skuul-500">
                        create a new account
                    </a>
                </p>
            </div>
            <div class="mt-8 sm:mt-10">
                <div class="mt-1 rounded-md shadow-sm">
                    <span class="block text-sm font-medium lead-6 text-gray-600">Email address</span>
                    <input id="email" type="email" name="email" required autofocus class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm" value="{{ old('email') }}" placeholder="Email address">
                </div>
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-skuul-600 hover:text-skuul-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>
                    <div class="mt-6 rounded-md shadow-sm">
                        <span class="block text-sm font-medium lead-6 text-gray-600">Password</span>
                        <input id="password" type="password" name="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm">
                    </div>
                    <div class="flex items-center justify-end mt-2">
                        <button type="submit" class="btn-primary w-full">
                            Sign in
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>