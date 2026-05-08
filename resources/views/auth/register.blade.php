<x-layout>
    <div class="auth-container">
        <h1>Register</h1>
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>

        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" autofocus required /><br>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required /><br>
            <input type="password" name="password" placeholder="Password" required /><br>
            <input type="password" name="password_confirmation" placeholder="Confirm password" required /><br>

            <button type="submit">Register</button>
        </form>
    </div>
</x-layout>