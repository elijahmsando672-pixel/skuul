<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field">
        </div>

        <button type="submit" class="btn-primary w-full">Send Password Reset Link</button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-skuul-600 hover:text-skuul-500">Back to login</a>
        </div>
    </form>
</x-guest-layout>
