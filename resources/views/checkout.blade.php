<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $platform->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Purchase</h1>

        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl mb-8">
            @if($platform->image_path)
                <img src="{{ asset('storage/' . $platform->image_path) }}" alt="{{ $platform->name }}" class="w-16 h-16 object-cover rounded-lg">
            @else
                <div class="w-16 h-16 bg-indigo-100 flex items-center justify-center rounded-lg">
                    <i class="fas fa-cube text-indigo-500"></i>
                </div>
            @endif
            <div>
                <div class="font-bold text-gray-900">{{ $platform->name }}</div>
                <div class="text-sm text-gray-500">v{{ $platform->version }}</div>
                <div class="text-indigo-600 font-bold">${{ number_format($platform->price, 2) }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.process', $platform->id) }}">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">Payment Method</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="flex items-center p-3 border border-indigo-600 bg-indigo-50 rounded-lg cursor-pointer">
                        <input type="radio" name="payment_method" checked class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-3 font-medium text-indigo-900">Credit Card (Mock)</span>
                    </label>
                </div>
            </div>

            @auth
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    Pay Now
                </button>
            @else
                <div class="text-center p-4 bg-yellow-50 rounded-lg text-yellow-800 text-sm">
                    Please <a href="/admin/login" class="font-bold underline">Login</a> to complete your purchase.
                </div>
            @endauth
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel and Return</a>
        </div>
    </div>

</body>
</html>
