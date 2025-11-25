@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-linear-to-br from-slate-50 via-blue-50 to-indigo-100 px-4 py-8 relative overflow-hidden">

        <!-- Sophisticated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Gradient orbs -->
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-linear-to-r from-blue-400/20 to-purple-500/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/2 -right-20 w-96 h-96 bg-linear-to-r from-emerald-400/15 to-cyan-500/15 rounded-full blur-3xl animate-float animation-delay-2000"></div>
            <div class="absolute -bottom-40 left-1/3 w-72 h-72 bg-linear-to-r from-violet-400/20 to-fuchsia-500/20 rounded-full blur-3xl animate-float animation-delay-4000"></div>

            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-size[64px_64px] [mask-radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <div class="w-full max-w-5xl relative z-10">

            <!-- Enhanced Header Section -->
            <div class="text-center mb-12 space-y-8">
                <div class="space-y-4">
                    <h1 class="text-5xl font-black bg-linear-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent tracking-tight">
                        WorkSafe'e Hoş Geldiniz
                    </h1>
                    <p class="text-xl text-slate-600 font-medium max-w-2xl mx-auto leading-relaxed">
                        İş güvenliği yönetiminizi dijitalleştirin, ekibinizle güvende kalın
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8">
                <div class="backdrop-blur-2xl bg-white/80 rounded-3xl shadow-2xl shadow-blue-500/20 p-8 border border-white/80">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-black text-slate-800">Hesap Oluştur</h2>
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center space-x-2 group transition-all">
                            <span>Zaten hesabınız var mı?</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <form action="{{ route('register') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Personal Information -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 pb-4 border-b border-slate-200/80">
                                <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Kişisel Bilgiler</h3>
                                    <p class="text-slate-500 text-sm">Sizinle ilgili temel bilgiler</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-user-circle text-blue-500 text-sm"></i>
                                        <span>Ad Soyad</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                            placeholder="Adınız ve soyadınız">
                                    </div>
                                    @error('name')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-envelope text-blue-500 text-sm"></i>
                                        <span>E-posta</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                            placeholder="ornek@email.com">
                                    </div>
                                    @error('email')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-lock text-blue-500 text-sm"></i>
                                        <span>Şifre</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="password" name="password" required
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300 pr-12"
                                            placeholder="En az 8 karakter">
                                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                            <i class="fas fa-eye text-slate-400 hover:text-blue-600 transition-colors" id="toggleIconPass"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-lock text-blue-500 text-sm"></i>
                                        <span>Şifre Tekrar</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="password" name="password_confirmation" required
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300 pr-12"
                                            placeholder="Şifrenizi tekrar girin">
                                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                            <i class="fas fa-eye text-slate-400 hover:text-blue-600 transition-colors" id="toggleIconConf"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Information -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 pb-4 border-b border-slate-200/80">
                                <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Firma Bilgileri</h3>
                                    <p class="text-slate-500 text-sm">Şirketinizin iletişim bilgileri</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Name -->
                                <div class="md:col-span-2 space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-building text-purple-500 text-sm"></i>
                                        <span>Firma Adı</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                            placeholder="Firma adınız">
                                    </div>
                                    @error('company_name')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Company Email -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-envelope text-purple-500 text-sm"></i>
                                        <span>Firma E-posta</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="email" name="company_email" value="{{ old('company_email') }}" required
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                            placeholder="info@firma.com">
                                    </div>
                                    @error('company_email')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Company Phone -->
                                <div class="space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-phone text-purple-500 text-sm"></i>
                                        <span>Firma Telefon <span class="text-slate-400 text-xs">(Opsiyonel)</span></span>
                                    </label>
                                    <div class="relative group">
                                        <input type="tel" name="company_phone" value="{{ old('company_phone') }}"
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300"
                                            placeholder="+90 555 123 4567">
                                    </div>
                                    @error('company_phone')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Company Address -->
                                <div class="md:col-span-2 space-y-3">
                                    <label class="text-sm font-semibold text-slate-700 flex items-center space-x-2">
                                        <i class="fas fa-map-marker-alt text-purple-500 text-sm"></i>
                                        <span>Firma Adresi <span class="text-slate-400 text-xs">(Opsiyonel)</span></span>
                                    </label>
                                    <div class="relative group">
                                        <textarea name="address" rows="3" value="{{ old('address') }}"
                                            class="w-full px-4 py-3.5 rounded-2xl border-2 border-slate-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 bg-white/80 placeholder-slate-400 text-slate-800 font-medium group-hover:border-slate-300 resize-none"
                                            placeholder="Firma adresinizi girin...">{{ old('address') }}</textarea>
                                    </div>
                                    @error('address')
                                        <div class="flex items-center space-x-2 text-sm text-red-600 bg-red-50 px-3 py-2.5 rounded-xl border border-red-200">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <span class="font-medium">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button type="submit"
                                class="w-full py-4 rounded-2xl bg-linear-to-r from-blue-600 to-indigo-600 font-semibold text-lg shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-3">
                                <span>Kaydı Tamamla</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldName) {
            const field = document.getElementsByName(fieldName)[0];
            const iconId = fieldName === 'password' ? 'toggleIconPass' : 'toggleIconConf';
            const icon = document.getElementById(iconId);

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

    <style>
        @keyframes float {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-20px) scale(1.05);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
@endsection
