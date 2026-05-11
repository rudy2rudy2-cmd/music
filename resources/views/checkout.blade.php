<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $platform->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-black text-slate-800">Checkout</h1>
            <i class="fas fa-lock text-slate-400"></i>
        </div>

        <div class="flex items-center space-x-4 p-5 bg-slate-50 rounded-2xl mb-8 border border-slate-100">
            @if($platform->image_path)
                <img src="{{ asset('storage/' . $platform->image_path) }}" alt="{{ $platform->name }}" class="w-20 h-20 object-cover rounded-xl shadow-sm">
            @else
                <div class="w-20 h-20 bg-indigo-100 flex items-center justify-center rounded-xl">
                    <i class="fas fa-cube text-indigo-500 text-3xl"></i>
                </div>
            @endif
            <div class="flex-1">
                <div class="font-bold text-slate-900 text-lg">{{ $platform->name }}</div>
                <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Version {{ $platform->version }}</div>
                <div class="text-indigo-600 font-black text-xl">${{ number_format($platform->price, 2) }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.process', $platform->id) }}">
            @csrf
            <div class="space-y-4 mb-8">
                <label class="block text-slate-700 text-sm font-bold mb-3">Select Payment Method</label>

                <label class="relative flex items-center p-4 border-2 border-indigo-600 bg-indigo-50/50 rounded-2xl cursor-pointer group transition-all">
                    <input type="radio" name="payment_method" value="stripe" checked class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                    <div class="ml-4 flex items-center justify-between w-full">
                        <span class="font-bold text-indigo-900">Credit Card</span>
                        <div class="flex space-x-2">
                            <i class="fab fa-cc-visa text-2xl text-slate-400"></i>
                            <i class="fab fa-cc-mastercard text-2xl text-slate-400"></i>
                        </div>
                    </div>
                </label>

                <label class="relative flex items-center p-4 border-2 border-slate-100 bg-white rounded-2xl cursor-pointer hover:border-slate-200 transition-all group">
                    <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500">
                    <div class="ml-4 flex items-center justify-between w-full">
                        <span class="font-bold text-slate-600 group-hover:text-slate-900">PayPal</span>
                        <i class="fab fa-paypal text-2xl text-slate-400"></i>
                    </div>
                </label>
            </div>

            @auth
                <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 active:scale-95">
                    Complete Purchase
                </button>
            @else
                <div class="text-center p-5 bg-amber-50 rounded-2xl text-amber-800 text-sm border border-amber-100">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Please <a href="/admin/login" class="font-black underline hover:text-amber-900">Login</a> to complete your purchase.
                </div>
            @endauth
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to home
            </a>
        </div>
    </div>

</body>
</html>
