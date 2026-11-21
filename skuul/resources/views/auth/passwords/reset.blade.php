<x-guest-layout>
    <x-slot name="title">Choose a New Password</x-slot>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field">
        </div>

        <div>
            <label for="password" class="form-label">New password</label>
            <input id="password" type="password" name="password" required class="input-field">
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="input-field">
        </div>

        <button type="submit" class="btn-primary w-full">Reset Password</button>
    </form>
</x-guest-layout>
