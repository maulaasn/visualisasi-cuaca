@extends('admin.layouts.sidebar')

@section('content')
    <div class="p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Berita Terbaru</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola informasi berita cuaca terkini</p>
            </div>
            <a href="{{ route('admin.news.create') }}" class="px-4 py-2.5 bg-[#1e3a8a] text-white rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors flex items-center gap-2 shadow-sm">
                <span>+ Berita Baru</span>
            </a>
        </div>

        <div class="space-y-4">
            @forelse($news as $item)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-col md:flex-row gap-5 items-center relative group">
                    <div class="shrink-0">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="Cover" class="w-full md:w-56 h-32 object-cover rounded-lg border border-slate-100">
                        @else
                            <div class="w-full md:w-56 h-32 bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center">
                                <span class="text-slate-400 text-sm">No Image</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 flex flex-col justify-center w-full">
                        <div class="flex items-center gap-3 mb-2.5">
                            <span @class([
                                'px-2.5 py-0.5 text-[11px] font-semibold rounded border text-center w-max',
                                'bg-orange-50 border-orange-200 text-orange-600' => $item->urgency_level == 'Peringatan Dini',
                                'bg-blue-50 border-blue-200 text-blue-600'     => $item->urgency_level == 'Prakiraan Cuaca',
                                'bg-purple-50 border-purple-200 text-purple-600' => $item->urgency_level == 'Berita Utama',
                                'bg-gray-50 border-gray-200 text-gray-600'     => !in_array($item->urgency_level, ['Peringatan Dini', 'Prakiraan Cuaca', 'Berita Utama'])
                            ])>
                                {{ $item->urgency_level }}
                            </span>
                            <span class="text-[13px] font-medium text-slate-400">{{ \Carbon\Carbon::parse($item->news_date)->translatedFormat('d F Y') }}</span>
                        </div>

                        <h2 class="text-[15px] font-bold text-slate-800 mb-1.5 leading-snug pr-16">
                            <a href="{{ route('admin.news.show', $item->id) }}" class="hover:text-blue-800 transition-colors">{{ $item->title }}</a>
                        </h2>
                        <p class="text-[13px] text-slate-500 line-clamp-2 pr-4">{{ Str::limit(strip_tags($item->content), 160) }}</p>

                        <div class="absolute top-4 right-4 flex items-center gap-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-200 bg-white p-1.5 rounded-lg border border-slate-100 shadow-sm">
                            <a href="{{ route('admin.news.edit', $item->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5h.036l12.196-12.196z" /></svg></a>
                            <button onclick="openDeleteModal('{{ route('admin.news.destroy', $item->id) }}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors cursor-pointer" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 border-dashed p-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-3">Belum Ada Berita</h3>
                    <p class="text-sm text-slate-500 mb-1 max-w-md">Saat ini belum ada data berita yang dipublikasikan. Silakan tambahkan berita baru untuk menampilkannya di sini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 transform scale-95 transition-all duration-300 m-4 relative" id="deleteModalContent">
            <button type="button" onclick="closeDeleteModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors">✕</button>
            <div class="flex flex-col items-center text-center mt-8">
                <p class="text-[15px] text-slate-500 mb-8 px-2">Apakah anda yakin ingin menghapus data berita ini?</p>
                <div class="flex gap-3 w-full">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-50 text-slate-700 text-sm font-bold rounded-lg border border-slate-300 shadow-sm cursor-pointer">Batal</button>
                    <form id="deleteForm" method="POST" class="flex-1 m-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg border border-red-700 shadow-sm transition-colors cursor-pointer">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(url) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('deleteForm').action = url;
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => { 
                modal.classList.remove('opacity-0'); 
                document.getElementById('deleteModalContent').classList.add('scale-100'); 
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('opacity-0'); 
            document.getElementById('deleteModalContent').classList.remove('scale-100');
            setTimeout(() => { 
                modal.classList.remove('flex'); modal.classList.add('hidden'); 
            }, 300);
        }

        document.getElementById('deleteForm').addEventListener('submit', function() {
            document.getElementById('deleteModal').classList.add('hidden');
        });
    </script>
@endsection