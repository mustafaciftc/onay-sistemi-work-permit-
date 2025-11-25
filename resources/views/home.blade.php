<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkSafe Pro - İş Güvenliği Danışmanlığı</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63398d 100%);
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 40;
        }

        .mobile-menu.active {
            display: block;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Feature Card Hover Effect */
        .feature-card {
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        @media (max-width: 768px) {
           .dashboard-btn {
            display: none;
           }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
      <nav class="fixed w-full bg-white/95 backdrop-blur-md z-50 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-hard-hat text-white text-lg"></i>
                    </div>
                    <span class="text-2xl font-black text-gray-900">WorkSafe<span
                            class="gradient-text">Pro</span></span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features"
                        class="text-gray-700 hover:text-purple-600 font-semibold transition-colors">Özellikler</a>
                    <a href="#products"
                        class="text-gray-700 hover:text-purple-600 font-semibold transition-colors">Ürünler</a>
                    <a href="#contact"
                        class="text-gray-700 hover:text-purple-600 font-semibold transition-colors">İletişim</a>
                </div>

                <!-- Auth Buttons -->
                @auth
                <div class="md:flex items-center space-x-4">

                    <a href="{{ route('admin.dashboard') }}"
                        class="dashboard-btn bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                        Dashboard
                    </a>
                         <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-sm font-medium">
                                Çıkış Yap
                            </button>
                        </form>
                </div>

                @else
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('login') }}"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                            Giriş
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                            Üye Ol
                        </a>
                    </div>
                @endauth

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden mobile-menu-btn p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu">
            <div class="px-4 py-6 space-y-4">
                <a href="#features" class="block text-gray-700 hover:text-purple-600 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors">Özellikler</a>
                <a href="#products" class="block text-gray-700 hover:text-purple-600 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors">Ürünler</a>
                <a href="#contact" class="block text-gray-700 hover:text-purple-600 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors">İletişim</a>
                <div class="pt-4 space-y-3 border-t border-gray-200">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300 shadow-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-gray-700 hover:bg-gray-100 text-center font-semibold px-6 py-3 rounded-lg transition-all duration-200 border border-gray-200">
                            Giriş
                        </a>
                        <a href="{{ route('register') }}" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300 shadow-lg">
                            Üye Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full glass-effect text-white/90 text-sm font-medium mb-6 animate-pulse">
                    <i class="fas fa-star mr-2"></i>İŞ GÜVENLİĞİNDE DİJİTAL DEVRİM
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight text-white px-4">
                    İş Güvenliğinizi
                    <span class="block mt-2 text-yellow-300">Dijitalleştirin</span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 mb-10 max-w-3xl mx-auto leading-relaxed px-4">
                    WorkSafe Pro ile iş izni süreçlerinizi otomatikleştirin, güvenliği artırın ve verimliliği maksimuma
                    çıkarın.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center px-4">
                    <a href="#"
                        class="group bg-white text-gray-900 px-8 py-4 rounded-xl font-bold hover:shadow-2xl transition-all duration-300 shadow-lg hover:-translate-y-1 inline-flex items-center justify-center w-full sm:w-auto">
                        <i class="fas fa-rocket mr-3 group-hover:scale-110 transition-transform"></i>
                        Ücretsiz Deneyin
                    </a>
                    <a href="#features"
                        class="group glass-effect px-8 py-4 rounded-xl font-bold hover:bg-white/20 transition-all duration-300 inline-flex items-center justify-center text-white w-full sm:w-auto">
                        <i class="fas fa-info-circle mr-3 group-hover:scale-110 transition-transform"></i>
                        Özelliklere Göz Atın
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 max-w-4xl mx-auto px-4">
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-black text-white mb-1">500+</div>
                        <div class="text-white/70 text-sm">Mutlu Müşteri</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-black text-white mb-1">10K+</div>
                        <div class="text-white/70 text-sm">İş İzni</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-black text-white mb-1">%99.9</div>
                        <div class="text-white/70 text-sm">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl font-black text-white mb-1">7/24</div>
                        <div class="text-white/70 text-sm">Destek</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    Neden <span class="gradient-text">WorkSafe Pro?</span>
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    Modern iş güvenliği yönetimi için tasarlanmış kapsamlı özellik seti
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Feature Card 1 -->
                <div
                    class="feature-card group bg-white rounded-2xl p-6 sm:p-8 hover-lift border-2 border-gray-100 hover:border-purple-200">
                    <div
                        class="w-16 h-16 btn-gradient rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-4">Mevzuata Tam Uyum</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        İSG mevzuatına %100 uyumlu, denetimlere hazır, güncel çözüm.
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Otomatik güncellemeler
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Denetim raporları
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Yasal uyum takibi
                        </li>
                    </ul>
                </div>

                <!-- Feature Card 2 -->
                <div
                    class="feature-card group bg-white rounded-2xl p-6 sm:p-8 hover-lift border-2 border-gray-100 hover:border-purple-200">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-4">Hızlı Kurulum</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        5 dakikada kurulum, aynı gün kullanıma başlayın.
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Rehberli kurulum
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Ücretsiz migrasyon
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            7/24 destek
                        </li>
                    </ul>
                </div>

                <!-- Feature Card 3 -->
                <div
                    class="feature-card group bg-white rounded-2xl p-6 sm:p-8 hover-lift border-2 border-gray-100 hover:border-purple-200">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-4">Mobil & Çapraz Platform</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Tüm cihazlardan kesintisiz erişim ve offline çalışma.
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Responsive tasarım
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Offline mod
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Anlık bildirimler
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    <span class="gradient-text">Çözümlerimiz</span>
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    İş güvenliği yönetiminizi dönüştürecek güçlü araçlar
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 lg:gap-8 max-w-6xl mx-auto">
                <!-- Product 1 -->
                <div
                    class="group bg-white rounded-3xl p-6 sm:p-8 hover-lift border-2 border-gray-100 shadow-lg hover:border-purple-200">
                    <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-6">
                        <div
                            class="w-20 h-20 btn-gradient rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                            <i class="fas fa-file-contract text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-3">İş İzni Yönetimi</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Çok aşamalı onay süreçleri ile güvenli iş izni yönetimi.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Otomatik onay
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    PDF raporlama
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Mobil imza
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    QR kod destek
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 2 -->
                <div
                    class="group bg-white rounded-3xl p-6 sm:p-8 hover-lift border-2 border-gray-100 shadow-lg hover:border-purple-200">
                    <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-6">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-3">Risk Analizi & Raporlama</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Otomatik risk değerlendirme ve detaylı analitik.
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Risk matrisi
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Önlem takibi
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    İstatistikler
                                </div>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Trend analizi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-gray-900 to-black">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black mb-6 text-white leading-tight">
                İş Güvenliğinizi<br>
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Dijitalleştirmeye
                    Hazır mısınız?</span>
            </h2>
            <p class="text-lg sm:text-xl text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
                WorkSafe Pro ile iş izni süreçlerinizi optimize edin ve çalışan güvenliğini en üst seviyeye çıkarın.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#"
                    class="group btn-gradient px-8 py-4 rounded-xl font-bold hover:shadow-2xl transition-all duration-300 shadow-lg hover:-translate-y-1 inline-flex items-center justify-center text-white">
                    <i class="fas fa-play mr-3 group-hover:scale-110 transition-transform"></i>
                    Ücretsiz Başlayın
                </a>
                <a href="#contact"
                    class="group border-2 border-gray-600 px-8 py-4 rounded-xl font-bold hover:border-purple-500 transition-all duration-300 text-white inline-flex items-center justify-center hover:bg-gray-800">
                    <i class="fas fa-phone-alt mr-3 group-hover:scale-110 transition-transform"></i>
                    Demo Talep Edin
                </a>
            </div>
            <p class="text-gray-400 mt-6 text-sm">
                14 gün ücretsiz deneme • Kredi kartı gerekmez • İstediğiniz zaman iptal
            </p>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 mb-4">
                    <span class="gradient-text">İletişime Geçin</span>
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    Size özel çözümlerimiz hakkında bilgi almak için bizimle iletişime geçin
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 max-w-6xl mx-auto">
                <!-- Contact Info -->
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-3xl p-6 sm:p-8 border border-blue-100">
                    <h3 class="text-2xl font-black text-gray-900 mb-6">İletişim Bilgileri</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 btn-gradient rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-lg">Adres</h4>
                                <p class="text-gray-600">İstanbul, Türkiye</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 btn-gradient rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-lg">Telefon</h4>
                                <p class="text-gray-600">+90 (212) 123 45 67</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 btn-gradient rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-lg">E-posta</h4>
                                <p class="text-gray-600">info@worksafe.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-gray-50 rounded-3xl p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-2xl font-black text-gray-900 mb-6">Mesaj Gönderin</h3>
                    <form class="space-y-6">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Adınız</label>
                                <input type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none bg-white"
                                    placeholder="Adınızı girin">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">E-posta</label>
                                <input type="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none bg-white"
                                    placeholder="E-posta adresiniz">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Konu</label>
                            <input type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none bg-white"
                                placeholder="Mesaj konusu">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mesajınız</label>
                            <textarea rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 outline-none bg-white resize-none"
                                placeholder="Mesajınızı yazın"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full btn-gradient text-white py-4 px-6 rounded-xl font-bold hover:shadow-xl transition-all duration-300 shadow-lg hover:-translate-y-0.5">
                            Mesaj Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div
                            class="w-8 h-8 bg-linear-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hard-hat text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-black text-white">WorkSafe<span
                                class="text-blue-400">Pro</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        İş güvenliğinde dijital dönüşümün lider çözüm ortağı. Modern teknolojilerle güvenli çalışma
                        ortamları oluşturuyoruz.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-lg mb-4">Ürün</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Özellikler</a></li>
                        <li><a href="#products" class="hover:text-white transition-colors">Çözümler</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Fiyatlandırma</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-lg mb-4">Destek</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Yardım Merkezi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">İletişim</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">SSS</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-lg mb-4">Şirket</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Hakkımızda</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kariyer</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Gizlilik</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Şartlar</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">&copy; 2025 WorkSafe Pro. Tüm hakları saklıdır.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            console.log('Mobile Menu Button:', mobileMenuBtn);
            console.log('Mobile Menu:', mobileMenu);

            function toggleMobileMenu() {
                console.log('Toggle clicked');
                const isActive = mobileMenu.classList.contains('active');

                if (isActive) {
                    // Menüyü kapat
                    mobileMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                    mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                    document.body.style.overflow = '';
                } else {
                    // Menüyü aç
                    mobileMenu.classList.add('active');
                    mobileMenuBtn.classList.add('active');
                    mobileMenuBtn.querySelector('i').classList.replace('fa-bars', 'fa-times');
                    document.body.style.overflow = 'hidden';
                }
            }

            // Mobile menu button click event
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Button clicked');
                    toggleMobileMenu();
                });

                // Close menu when clicking on links
                const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.remove('active');
                        mobileMenuBtn.classList.remove('active');
                        mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                        document.body.style.overflow = '';
                    });
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (mobileMenu.classList.contains('active') &&
                        !mobileMenu.contains(e.target) &&
                        !mobileMenuBtn.contains(e.target)) {
                        mobileMenu.classList.remove('active');
                        mobileMenuBtn.classList.remove('active');
                        mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                        document.body.style.overflow = '';
                    }
                });

                // Close menu on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                        mobileMenu.classList.remove('active');
                        mobileMenuBtn.classList.remove('active');
                        mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');

                    if (href === '#') return;

                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();

                        // Close mobile menu if open
                        if (mobileMenu && mobileMenu.classList.contains('active')) {
                            mobileMenu.classList.remove('active');
                            mobileMenuBtn.classList.remove('active');
                            mobileMenuBtn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                            document.body.style.overflow = '';
                        }

                        const headerHeight = 80;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
