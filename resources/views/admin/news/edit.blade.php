@extends('admin.layouts.sidebar')

@section('content')
<div class="p-8 w-full h-full flex flex-col">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex-1 flex flex-col min-h-0">
        <div class="flex justify-between items-start mb-5 border-b border-slate-100 pb-3 shrink-0">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Edit Berita</h1>
                <p class="text-slate-500 text-sm mt-0.5">Ubah informasi berita cuaca yang sudah ada</p>
            </div>
            <a href="{{ route('admin.news.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">✕</a>
        </div>

        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col space-y-4 min-h-0">
            @csrf @method('PUT')
            
            <div class="shrink-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Utama (Opsional)</label>
                <div class="flex items-center gap-4">
                    @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" class="w-20 h-14 object-cover rounded-md border border-slate-200 shrink-0 shadow-sm">
                    @endif
                    <div class="flex-1">
                        <input type="file" name="image" class="block w-full text-sm text-slate-500 border border-slate-200 rounded-lg bg-white cursor-pointer hover:bg-slate-50 transition-colors file:mr-4 file:py-2 file:px-4 file:border-0 file:border-r file:border-slate-200 file:bg-slate-50 file:text-slate-700 file:text-sm file:font-semibold">
                    </div>
                </div>
            </div>

            <div class="shrink-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Berita</label>
                <input type="text" name="title" value="{{ $news->title }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div class="grid grid-cols-2 gap-4 shrink-0">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Level Urgensi</label>
                    <select name="urgency_level" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="Berita Utama" {{ $news->urgency_level == 'Berita Utama' ? 'selected' : '' }}>Berita Utama</option>
                        <option value="Prakiraan Cuaca" {{ $news->urgency_level == 'Prakiraan Cuaca' ? 'selected' : '' }}>Prakiraan Cuaca</option>
                        <option value="Peringatan Dini" {{ $news->urgency_level == 'Peringatan Dini' ? 'selected' : '' }}>Peringatan Dini</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="news_date" value="{{ $news->news_date }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-blue-600">
                </div>
            </div>

            <div class="flex-1 flex flex-col min-h-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Detail Konten</label>
                <textarea name="content" required class="flex-1 w-full px-4 py-2.5 rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-blue-600 resize-none">{{ $news->content }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 shrink-0">
                <a href="{{ route('admin.news.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#1e3a8a] text-white text-sm font-semibold hover:bg-blue-800 shadow-sm transition-colors cursor-pointer">Update Berita</button>
            </div>
        </form>
    </div>
</div>
@endsection