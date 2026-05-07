<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Platform Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">My Platform Store</a>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Licenses</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Platform</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">License Key</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Domain</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($licenses as $license)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $license->platform->name }}</div>
                                <div class="text-xs text-gray-500">v{{ $license->platform->version }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $license->license_key }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold rounded {{ $license->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($license->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $license->domain ?? 'Not activated yet' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.download', $license->license_key) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                You haven't purchased any licenses yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
