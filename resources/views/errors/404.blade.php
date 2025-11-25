<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Bulunamadı - WorkSafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-blink {
            animation: blink 2s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="font-sans bg-linear-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- linear Orbs -->
        <div
            class="absolute -top-20 -left-20 w-72 h-72 bg-linear-to-r from-blue-400/20 to-cyan-500/20 rounded-full blur-3xl animate-float">
        </div>
        <div
            class="absolute top-1/2 -right-20 w-80 h-80 bg-linear-to-r from-purple-400/15 to-pink-500/15 rounded-full blur-3xl animate-float animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-20 left-1/4 w-64 h-64 bg-linear-to-r from-green-400/20 to-emerald-500/20 rounded-full blur-3xl animate-float animation-delay-4000">
        </div>

        <!-- Grid Pattern -->
        <div
            class="absolute inset-0 bg-[linear-linear(rgba(99,102,241,0.03)_1px,transparent_1px),linear-linear(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg[64px_64px] [mask-radial-linear(ellipse_80%_50%_at_50%_50%,black,transparent)]">
        </div>
    </div>

    <div class="max-w-2xl w-full relative z-10">
        <!-- Main Content -->
        <div class="text-center">
            <!-- Animated 404 Number -->
            <div class="relative mb-8">
                <div
                    class="text-9xl font-black bg-linear-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    4<span class="animate-blink">0</span>4
                </div>
                <div class="absolute -top-4 -right-4 w-8 h-8 bg-red-500 rounded-full animate-ping"></div>
                <div class="absolute -bottom-2 -left-4 w-6 h-6 bg-yellow-500 rounded-full animate-pulse-slow"></div>
            </div>

            <!-- Icon -->
            <div class="mb-8">
                <div
                    class="w-24 h-24 mx-auto bg-linear-to-br from-red-500 to-orange-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-red-500/30 mb-6">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
            </div>

            <!-- Message -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                    Sayfa Bulunamadı!
                </h1>
                <p class="text-lg text-slate-600 mb-6 max-w-md mx-auto leading-relaxed">
                    Aradığınız sayfa taşınmış, silinmiş veya geçici olarak kullanılamıyor olabilir.
                    Doğru sayfada olduğunuzdan emin olmak için URL'yi kontrol edin.
                </p>

                <!-- Technical Details -->
                <div
                    class="bg-white/50 backdrop-blur-sm rounded-2xl p-4 max-w-sm mx-auto border border-white/80 shadow-sm">
                    <div class="flex items-center justify-center space-x-2 text-sm text-slate-500">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Hata Kodu: 404 - Not Found</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                <a href="{{ url('/') }}"
                    class="group bg-linear-to-r from-blue-600 to-indigo-60 px-8 py-4 rounded-2xl font-semibold shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-300 hover:-translate-y-1 active:scale-95 flex items-center space-x-3">
                    <i class="fas fa-home text-sm"></i>
                    <span>Ana Sayfaya Dön</span>
                </a>

                <a href="javascript:history.back()"
                    class="group bg-white text-slate-700 px-8 py-4 rounded-2xl font-semibold border-2 border-slate-200 hover:border-blue-500 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 active:scale-95 flex items-center space-x-3">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Geri Git</span>
                </a>
            </div>

            <!-- Additional Help -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 max-w-md mx-auto border border-white/80 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center justify-center space-x-2">
                    <i class="fas fa-life-ring text-blue-500"></i>
                    <span>Yardıma mı ihtiyacınız var?</span>
                </h3>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="mailto:destek@worksafe.com"
                        class="text-slate-600 hover:text-blue-600 transition-colors flex items-center space-x-2 justify-center text-sm">
                        <i class="fas fa-envelope"></i>
                        <span>destek@worksafe.com</span>
                    </a>
                    <span class="text-slate-300 hidden sm:block">•</span>
                    <a href="tel:+902121234567"
                        class="text-slate-600 hover:text-blue-600 transition-colors flex items-center space-x-2 justify-center text-sm">
                        <i class="fas fa-phone"></i>
                        <span>+90 212 123 45 67</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <div
                    class="w-8 h-8 bg-linear-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hard-hat text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold text-slate-700">WorkSafe</span>
            </div>
            <p class="text-slate-500 text-sm">
                İş Güvenliği Yönetim Sistemi • © {{ date('Y') }} Tüm hakları saklıdır.
            </p>
        </div>
    </div>

    <!-- Floating Elements -->
    <div class="fixed bottom-8 right-8">
        <div class="w-6 h-6 bg-green-400 rounded-full animate-ping"></div>
    </div>
    <div class="fixed top-8 left-8">
        <div class="w-4 h-4 bg-purple-400 rounded-full animate-pulse"></div>
    </div>

    <script>
        // Add animation delays dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .animation-delay-2000 { animation-delay: 2s; }
                .animation-delay-4000 { animation-delay: 4s; }
            `;
            document.head.appendChild(style);
        });

        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const numbers = document.querySelectorAll('.text-9xl span');
            numbers.forEach((number, index) => {
                number.style.animationDelay = `${index * 0.5}s`;
            });
        });
    </script>
</body>

</html>
