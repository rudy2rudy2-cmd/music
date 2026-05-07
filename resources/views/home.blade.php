<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --header-bg: {{ $headerColor }};
            --footer-bg: {{ $footerColor }};
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-[var(--header-bg)] shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="h-10">
                @else
                    <span class="text-2xl font-bold text-indigo-600">{{ $siteName }}</span>
                @endif
            </div>
            <nav class="hidden md:flex space-x-8 text-gray-700 font-medium">
                <a href="#" class="hover:text-indigo-600">Home</a>
                <a href="#platforms" class="hover:text-indigo-600">Platforms</a>
                <a href="#" class="hover:text-indigo-600">About</a>
            </nav>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 font-medium">Dashboard</a>
                @else
                    <a href="/admin/login" class="text-gray-700 font-medium">Login</a>
                    <a href="#" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-medium hover:bg-indigo-700 transition">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-6">Build Your Digital Empire Faster</h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">Discover premium, ready-to-deploy platforms and SaaS kits designed for high performance and scalability.</p>
            <div class="flex justify-center space-x-4">
                <a href="#platforms" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">Browse Catalog</a>
                <a href="#" class="bg-gray-100 text-gray-800 px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition">View Demo</a>
            </div>
        </div>
    </section>

    <!-- Platforms Section -->
    <section id="platforms" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Available Platforms</h2>
                <div class="h-1 w-20 bg-indigo-600 mx-auto rounded"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($platforms as $platform)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl transition duration-300">
                        @if($platform->image_path)
                            <img src="{{ asset('storage/' . $platform->image_path) }}" alt="{{ $platform->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-cubes text-5xl text-indigo-300"></i>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $platform->name }}</h3>
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded">v{{ $platform->version ?? '1.0' }}</span>
                            </div>
                            <p class="text-gray-600 mb-6 h-12 overflow-hidden">{{ $platform->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($platform->price, 2) }}</span>
                                <a href="{{ route('checkout', $platform->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">Buy Now</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-gray-500 text-xl">No platforms available at the moment. Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[var(--footer-bg)] py-12 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center space-x-6 mb-8 text-gray-500">
                <a href="#" class="hover:text-indigo-600"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="hover:text-indigo-600"><i class="fab fa-github text-xl"></i></a>
                <a href="#" class="hover:text-indigo-600"><i class="fab fa-linkedin text-xl"></i></a>
            </div>
            <p class="text-gray-600">&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
