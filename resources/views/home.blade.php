<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root {
            --header-bg: {{ $headerColor }};
            --footer-bg: {{ $footerColor }};
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(165, 180, 252, 0.15), transparent);
        }
    </style>
</head>
<body class="bg-white flex flex-col min-h-screen antialiased text-slate-900">

    <!-- Header -->
    <header class="bg-[var(--header-bg)]/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="h-10">
                @else
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class="fas fa-rocket text-white"></i>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-slate-900">{{ $siteName }}</span>
                    </div>
                @endif
            </div>
            <nav class="hidden md:flex space-x-10 text-slate-600 font-bold text-sm uppercase tracking-widest">
                <a href="#" class="hover:text-indigo-600 transition-colors">Home</a>
                <a href="#platforms" class="hover:text-indigo-600 transition-colors">Platforms</a>
                <a href="#" class="hover:text-indigo-600 transition-colors">About</a>
            </nav>
            <div class="flex items-center space-x-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="font-bold text-slate-900 flex items-center">
                         <span class="mr-2">Dashboard</span>
                         <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                @else
                    <a href="/admin/login" class="text-slate-600 font-bold hover:text-slate-900">Login</a>
                    <a href="/admin/register" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition shadow-xl shadow-slate-200">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-32 hero-gradient">
        <div class="container mx-auto px-6 relative z-10 text-center">
            <span class="inline-block py-2 px-4 rounded-full bg-indigo-50 text-indigo-600 text-xs font-black uppercase tracking-widest mb-6">Premium Software Marketplace</span>
            <h1 class="text-6xl md:text-8xl font-black text-slate-900 mb-8 leading-tight tracking-tighter">
                Deploy Your Next <br> <span class="text-indigo-600">Big Idea</span> Faster.
            </h1>
            <p class="text-xl text-slate-500 mb-12 max-w-2xl mx-auto leading-relaxed">
                Discover premium, ready-to-deploy platforms and SaaS kits designed for high performance, scalability, and beautiful user experience.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="#platforms" class="bg-indigo-600 text-white px-10 py-5 rounded-2xl font-black text-lg hover:bg-indigo-700 transition shadow-2xl shadow-indigo-200 w-full sm:w-auto">
                    Browse Catalog
                </a>
                <a href="#" class="bg-white text-slate-900 border-2 border-slate-100 px-10 py-5 rounded-2xl font-black text-lg hover:bg-slate-50 transition w-full sm:w-auto">
                    View Live Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Platforms Section -->
    <section id="platforms" class="py-32">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-20">
                <div class="max-w-2xl">
                    <h2 class="text-4xl font-black text-slate-900 mb-6">Ready-to-use Solutions</h2>
                    <p class="text-slate-500 text-lg">Pick the perfect foundation for your next project. All platforms come with full source code and lifetime updates.</p>
                </div>
                <div class="mt-8 md:mt-0">
                     <div class="h-1 w-24 bg-indigo-600 rounded-full"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($platforms as $platform)
                    <div class="group bg-white rounded-3xl p-4 border border-slate-100 hover:border-indigo-100 hover:shadow-2xl transition-all duration-500">
                        <div class="relative rounded-2xl overflow-hidden mb-6 h-64 bg-slate-50">
                            @if($platform->image_path)
                                <img src="{{ asset('storage/' . $platform->image_path) }}" alt="{{ $platform->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-cubes text-6xl text-slate-200"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-md text-slate-900 text-xs font-black px-3 py-1.5 rounded-full shadow-sm">v{{ $platform->version ?? '1.0' }}</span>
                            </div>
                        </div>

                        <div class="px-4 pb-4">
                            <h3 class="text-2xl font-black text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors">{{ $platform->name }}</h3>
                            <p class="text-slate-500 mb-8 line-clamp-2 leading-relaxed">{{ $platform->description }}</p>

                            <div class="flex justify-between items-center border-t border-slate-50 pt-6">
                                <div>
                                    <span class="text-slate-400 text-xs font-bold uppercase block mb-1">Starting at</span>
                                    <span class="text-3xl font-black text-slate-900">${{ number_format($platform->price, 2) }}</span>
                                </div>
                                <a href="{{ route('checkout', $platform->id) }}" class="bg-slate-900 text-white p-4 rounded-2xl hover:bg-indigo-600 transition shadow-lg active:scale-95">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-32 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <i class="fas fa-search text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-500 text-xl font-bold">No platforms available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[var(--footer-bg)] py-20 mt-auto border-t border-slate-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16 text-center md:text-left">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-6 justify-center md:justify-start">
                        <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-rocket text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-900">{{ $siteName }}</span>
                    </div>
                    <p class="text-slate-500 max-w-sm mx-auto md:mx-0 leading-relaxed">
                        The ultimate marketplace for high-quality software solutions and digital platforms. Empowering creators since {{ date('Y') }}.
                    </p>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 mb-6 uppercase text-xs tracking-widest">Resources</h4>
                    <ul class="space-y-4 text-slate-500 font-medium">
                        <li><a href="#" class="hover:text-indigo-600">Documentation</a></li>
                        <li><a href="#" class="hover:text-indigo-600">Help Center</a></li>
                        <li><a href="#" class="hover:text-indigo-600">License Terms</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-black text-slate-900 mb-6 uppercase text-xs tracking-widest">Connect</h4>
                    <div class="flex justify-center md:justify-start space-x-4">
                        <a href="#" class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm"><i class="fab fa-github"></i></a>
                        <a href="#" class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition shadow-sm"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="pt-12 border-t border-slate-200/60 text-center flex flex-col md:flex-row justify-between items-center text-sm text-slate-400 font-medium">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
                <div class="mt-4 md:mt-0 space-x-6">
                    <a href="#" class="hover:text-slate-600">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-600">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
