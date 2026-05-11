<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="text-center">
        <div class="relative inline-block mb-8">
            <div class="w-24 h-24 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fab fa-{{ $method === 'paypal' ? 'paypal' : 'stripe-s' }} text-2xl text-indigo-600"></i>
            </div>
        </div>

        <h1 class="text-2xl font-black text-slate-900 mb-2">Connecting to {{ ucfirst($method) }}</h1>
        <p class="text-slate-500 mb-8">Please do not refresh the page while we process your secure transaction.</p>

        <form id="payment-form" method="POST" action="{{ route('payment.success', $platform->id) }}">
            @csrf
            <button type="submit" class="text-indigo-600 font-bold hover:underline">
                Click here if you are not redirected automatically...
            </button>
        </form>
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('payment-form').submit();
        }, 2000);
    </script>

</body>
</html>
