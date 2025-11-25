@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Form Şablonları</h1>
                <a href="{{ route('form-templates.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Yeni Şablon Oluştur
                </a>
            </div>

            @if ($templates->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($templates as $template)
                        <div class="bg-white rounded-lg shadow border border-gray-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                    @if ($template->is_default)
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                            Varsayılan
                                        </span>
                                    @endif
                                </div>

                                @if ($template->description)
                                    <p class="text-gray-600 mb-4">{{ $template->description }}</p>
                                @endif

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Alan Sayısı:</span>
                                        <span class="font-medium">{{ count($template->fields) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Onay Adımları:</span>
                                        <span class="font-medium">{{ count($template->workflow) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Durum:</span>
                                        <span class="{{ $template->is_active ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $template->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('form-templates.show', $template) }}"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            Görüntüle
                                        </a>
                                        <a href="{{ route('form-templates.edit', $template) }}"
                                            class="text-green-600 hover:text-green-900 text-sm">
                                            Düzenle
                                        </a>
                                    </div>

                                    @if (!$template->is_default)
                                        <form action="{{ route('form-templates.set-default', $template) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 text-sm">
                                                Varsayılan Yap
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz form şablonunuz yok</h3>
                    <p class="text-gray-600 mb-4">İş izni formlarınızı özelleştirmek için şablon oluşturun.</p>
                    <a href="{{ route('form-templates.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        İlk Şablonu Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
