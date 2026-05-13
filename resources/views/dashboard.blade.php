<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased text-slate-900">

    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside class="bg-indigo-900 text-white w-64 flex-shrink-0 transition-all duration-300" :class="{ '-ml-64': !sidebarOpen }">
            <div class="p-6">
                <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter flex items-center">
                    <i class="fas fa-rocket mr-3 text-indigo-400"></i>
                    <span>PLATFORM</span>
                </a>
            </div>

            <nav class="mt-6 px-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 bg-indigo-800 rounded-xl text-white font-bold">
                    <i class="fas fa-th-large w-6"></i>
                    <span>My Licenses</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white rounded-xl transition-colors font-medium group">
                    <i class="fas fa-user w-6 group-hover:text-indigo-400"></i>
                    <span>Profile Settings</span>
                </a>
                @if(Auth::user()->isAdmin())
                <a href="/admin" class="flex items-center px-4 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white rounded-xl transition-colors font-medium group">
                    <i class="fas fa-user-shield w-6 group-hover:text-indigo-400"></i>
                    <span>Admin Panel</span>
                </a>
                @endif
            </nav>

            <div class="absolute bottom-0 w-64 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-indigo-300 hover:text-white hover:bg-indigo-800 rounded-xl transition-all font-medium">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
                <div class="px-8 py-4 flex justify-between items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-indigo-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <div class="flex items-center space-x-4">
                        <div class="text-right mr-3 hidden sm:block">
                            <div class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold border-2 border-indigo-200">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="flex justify-between items-end mb-10">
                        <div>
                            <h1 class="text-4xl font-black text-slate-900 mb-2">Digital Products</h1>
                            <p class="text-slate-500">Manage your purchased platforms and license keys.</p>
                        </div>
                        <a href="{{ route('home') }}#platforms" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                            <i class="fas fa-plus mr-2"></i>Buy New Platform
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-xl">
                            <div class="flex">
                                <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3"></i>
                                <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6">
                        @forelse($licenses as $license)
                            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                                <div class="p-8 flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
                                    <div class="w-24 h-24 bg-indigo-50 rounded-2xl flex items-center justify-center flex-shrink-0 border border-indigo-100">
                                        @if($license->platform->image_path)
                                            <img src="{{ asset('storage/' . $license->platform->image_path) }}" class="w-full h-full object-cover rounded-2xl">
                                        @else
                                            <i class="fas fa-cube text-4xl text-indigo-300"></i>
                                        @endif
                                    </div>

                                    <div class="flex-1 text-center md:text-left">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2">
                                            <h3 class="text-2xl font-black text-slate-900">{{ $license->platform->name }}</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest {{ $license->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $license->status }}
                                            </span>
                                        </div>
                                        <div class="text-slate-500 mb-6">Version {{ $license->platform->version }} • Purchased on {{ $license->created_at->format('M d, Y') }}</div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">License Key</div>
                                                <div class="font-mono text-sm text-indigo-600 font-bold break-all">{{ $license->license_key }}</div>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Domain Binding</div>
                                                <div class="text-sm font-bold text-slate-700">{{ $license->domain ?? 'Pending Activation' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col space-y-3 w-full md:w-auto">
                                        <a href="{{ route('platform.download', $license->license_key) }}" class="flex items-center justify-center px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition">
                                            <i class="fas fa-download mr-2"></i> Download ZIP
                                        </a>
                                        <a href="#" class="flex items-center justify-center px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition">
                                            <i class="fas fa-book mr-2"></i> Documentation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-16 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-box-open text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2">No products found</h3>
                                <p class="text-slate-500 mb-8 max-w-sm mx-auto">You haven't purchased any platforms yet. Explore our catalog to find the perfect solution for your business.</p>
                                <a href="{{ route('home') }}" class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-700">
                                    Browse Platforms <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>
