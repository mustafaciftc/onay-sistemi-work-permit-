<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'İş İzni Sistemi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @auth
        <meta name="user-id" content="{{ Auth::id() }}">
        <meta name="company-id" content="{{ session('current_company_id') }}">
    @endauth

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <i class="fas fa-hard-hat text-blue-600 text-2xl"></i>
                        <span class="text-xl font-bold text-gray-800">İş İzni Sistemi</span>
                    </a>

                </div>

                <div class="flex items-center space-x-6">
                    @auth

                        <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-sm font-medium">
                                Çıkış Yap
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="bg-linear-to-r from-blue-600 to-purple-600 px-6 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                            Giriş
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-linear-to-r from-blue-600 to-purple-600 px-6 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                            Üye Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @stack('scripts')

    <script>
        function toggleCompanyDropdown() {
            document.getElementById('companyDropdown').classList.toggle('hidden');
        }

        // Tıklayınca dışarıya tıklanırsa kapat
        document.addEventListener('click', function(e) {
            if (!e.target.closest('nav')) {
                document.getElementById('companyDropdown')?.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
