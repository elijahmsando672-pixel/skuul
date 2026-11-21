<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6">
        @csrf

        <div class="rounded-md bg-skuul-50 dark:bg-slate-900 px-4 py-5 shadow sm:px-10">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-slate-100">
                    Create an account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-slate-100">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-skuul-600 hover:text-skuul-500">
                        Sign in
                    </a>
                </p>
            </div>
            <div class="mt-8 sm:mt-10">
                <div class="mt-1 rounded-md shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <span class="block text-sm font-medium leading-6 text-gray-600 dark:text-slate-100">Full Name</span>
                            <input id="name" type="text" name="name" required autofocus class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm" value="{{ old('name') }}">
                        </div>
                        <div>
                            <span class="block text-sm font-medium leading-6 text-gray-600 dark:text-slate-100">Email</span>
                            <input id="email" type="email" name="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>
                <div class="mt-6 rounded-md shadow-sm">
                    <div>
                        <span class="block text-sm font-medium leading-6 text-gray-600 dark:text-slate-100">Password</span>
                        <input id="password" type="password" name="password" required minlength="8" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm">
                    </div>
                    <div class="mt-4">
                        <span class="block text-sm font-medium leading-6 text-gray-600 dark:text-slate-100">Confirm Password</span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm">
                    </div>
                </div>
                <div class="mt-6 rounded-md shadow-sm">
                    <span class="block text-sm font-medium leading-6 text-gray-600 dark:text-slate-100">Role</span>
                    <select id="role_type" name="role_type" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-skuul-500 focus:border-skuul-500 sm:text-sm">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="parent">Parent</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="btn-primary w-full">
                        Create account
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>