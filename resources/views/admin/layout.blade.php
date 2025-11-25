<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - İş İzni Sistemi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <i class="fas fa-hard-hat text-blue-600 text-xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">İş İzni Sistemi</span>
                    </a>

                    @auth
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ route('admin.work-permits.index') }}"
                                class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">İş
                                İzinleri</a>
                            <a href="{{ route('admin.work-permits.create') }}"
                                class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Yeni
                                İş İzni</a>

                        </div>
                    </div>
                    @endauth
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Çıkış
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                    @else
                    <a href="{{ route('login') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Giriş Yap</a>
                    <a href="{{ route('register') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">Kayıt
                        Ol</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
