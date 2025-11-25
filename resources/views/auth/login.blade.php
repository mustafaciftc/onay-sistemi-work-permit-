@extends('layouts.app')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-linear-to-br from-slate-50 via-blue-50 to-indigo-100 px-4 py-8 relative overflow-hidden">

        <!-- Background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-20 -right-20 w-72 h-72 bg-linear-to-r from-blue-400/20 to-cyan-500/20 rounded-full blur-3xl animate-float">
            </div>
            <div
                class="absolute top-1/2 -left-20 w-80 h-80 bg-linear-to-r from-emerald-400/15 to-teal-500/15 rounded-full blur-3xl animate-float animation-delay-3000">
            </div>
            <div
                class="absolute -bottom-32 right-1/4 w-64 h-64 bg-linear-to-r from-violet-400/20 to-purple-500/20 rounded-full blur-3xl animate-float animation-delay-6000">
            </div>

            <!-- Grid pattern -->
            <div
                class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-size[64px_64px] [mask-radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]">
            </div>
        </div>

        <div class="w-full max-w-md relative z-10">

            <!-- Login Card -->
            <div class="backdrop-blur-2xl bg-white/80 rounded-3xl shadow-2xl shadow-blue-500/20 p-8 border border-white/80">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Hoş Geldiniz! 👋</h2>
                    <p class="text-slate-600 text-sm">Hesabınıza erişmek için bilgilerinizi girin</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-3">
                        <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                            <i class="fas fa-envelope text-blue-500 text-sm"></i>
                            <span>E-posta Adresi</span>
                        </label>
                        <div class="relative group">
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                placeholder="ornek@email.com">
                        </div>
                        @error('email')
                            <div
                                class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                <i class="fas fa-exclamation-circle"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                <i class="fas fa-lock text-blue-500 text-sm"></i>
                                <span>Şifre</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-all flex items-center space-x-1 group">
                                    <span>Şifremi unuttum</span>
                                    <i
                                        class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endif
                        </div>
                        <div class="relative group">
                            <input type="password" name="password" required
                                class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300 pr-12"
                                placeholder="••••••••••">
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="fas fa-eye text-slate-400 hover:text-blue-600 transition-colors"
                                    id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div
                                class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                <i class="fas fa-exclamation-circle"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="space-y-6">

                        <button type="submit"
                            class="w-full py-4 rounded-2xl text-dark font-bold text-lg bg-linear-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl hover:shadow-blue-500/30 transform hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-3 group">
                            <span>Giriş Yap</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-3 bg-white text-slate-500 font-semibold text-xs">veya</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-slate-600 text-sm mb-3">Henüz bir hesabınız yok mu?</p>
                    <a href="{{ route('register') }}"
                        class="w-full py-4 rounded-2xl text-dark font-bold text-lg bg-linear-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl hover:shadow-blue-500/30 transform hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-3 group">
                        <i class="fas fa-user-plus text-sm group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm">Yeni Hesap Oluştur</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementsByName('password')[0];
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-10px) scale(1.02);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animation-delay-3000 {
            animation-delay: 3s;
        }

        .animation-delay-6000 {
            animation-delay: 6s;
        }
    </style>
@endsection
