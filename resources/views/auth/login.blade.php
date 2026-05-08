<x-layout>
    <div class="auth-container">
        <h1>Login</h1>
        <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>

        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus required ><br>
            <input type="password" name="password" placeholder="Password" value="{{ old('password') }}" required><br>

            <button type="submit">Login</button>
        </form>

        <div class="default-users">
            <h2>Default Users</h2>

            <p>
                <strong>Email:</strong> sanija@gmail.com<br>
                <strong>Password:</strong> password123
            </p>
            <hr>

            <p>
                <strong>Email:</strong> tkeita@gmail.com<br>
                <strong>Password:</strong> password123
            </p>
        </div>
    </div>
</x-layout>