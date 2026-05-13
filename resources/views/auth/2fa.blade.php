<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Two-Factor Authentication</h1>
        <p class="text-gray-600 mb-6 text-center text-sm">
            Please enter the 6-digit code from your authenticator app to continue.
        </p>

        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf
            <div class="mb-6">
                <input type="text" name="one_time_password" required autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest"
                    placeholder="000000">
                @error('one_time_password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-colors">
                Verify Code
            </button>
        </form>

        <div class="mt-6 text-center">
             <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
