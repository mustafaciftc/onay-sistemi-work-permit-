@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Paylaşım Linkleri</h1>
                    <p class="text-gray-600">{{ $workPermit->title }}</p>
                </div>
                <a href="{{ route('admin.work-permits.shareable-links.create', $workPermit) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Yeni Link Oluştur
                </a>
            </div>

            @if (session('new_link_url'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-green-800 font-semibold">Yeni Paylaşım Linki Oluşturuldu!</h4>
                            <p class="text-green-700 text-sm mt-1">Aşağıdaki linki paylaşabilirsiniz:</p>
                        </div>
                        <button onclick="copyToClipboard('{{ session('new_link_url') }}')"
                            class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                            Linki Kopyala
                        </button>
                    </div>
                    <div class="mt-2 p-2 bg-white rounded border">
                        <code class="text-sm text-gray-800 break-all">{{ session('new_link_url') }}</code>
                    </div>
                </div>
            @endif

            @if ($links->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach ($links as $link)
                            <li class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="shrink-0">
                                            <i class="fas fa-link text-gray-400 text-xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $link->getShareUrl() }}
                                                </p>
                                                @if (!$link->is_active)
                                                    <span
                                                        class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                                        Pasif
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                @if ($link->expires_at)
                                                    Son kullanım: {{ $link->expires_at->format('d.m.Y H:i') }}
                                                @endif
                                                @if ($link->max_views)
                                                    • Maksimum: {{ $link->max_views }} görüntüleme
                                                @endif
                                                • Görüntülenme: {{ $link->view_count }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button onclick="copyToClipboard('{{ $link->getShareUrl() }}')"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            Kopyala
                                        </button>

                                        <form action="{{ route('admin.shareable-links.toggle', $link) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-{{ $link->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $link->is_active ? 'yellow' : 'green' }}-900 text-sm">
                                                {{ $link->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.shareable-links.destroy', $link) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Bu paylaşım linkini silmek istediğinizden emin misiniz?')"
                                                class="text-red-600 hover:text-red-900 text-sm">
                                                Sil
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <i class="fas fa-share-alt text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz paylaşım linkiniz yok</h3>
                    <p class="text-gray-600 mb-4">İş iznini dışarıdaki kişilerle paylaşmak için link oluşturun.</p>
                    <a href="{{ route('admin.work-permits.shareable-links.create', $workPermit) }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        İlk Linki Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Başarılı mesajı göster
                showToast('Link panoya kopyalandı!', 'success');
            }, function(err) {
                console.error('Kopyalama hatası: ', err);
            });
        }

        function showToast(message, type = 'info') {
            // Toast mesajı göster
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-blue-500'
    } z-50`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }
    </script>
@endsection
