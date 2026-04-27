@extends('admin.layouts.sidebar')

@section('content')
<div class="p-8 w-full h-full flex flex-col">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex-1 flex flex-col">
        <div class="flex justify-between items-start mb-5 border-b border-slate-100 pb-3 shrink-0">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Tambah Berita Baru</h1>
                <p class="text-slate-500 text-sm mt-0.5">Buat berita baru untuk dipublikasikan</p>
            </div>
            <a href="{{ route('admin.news.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">✕</a>
        </div>

        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col space-y-5">
            @csrf
            
            <div class="shrink-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Utama (Opsional)</label>
                <input type="file" name="image" class="block w-full text-sm text-slate-500 border border-slate-200 rounded-lg bg-white cursor-pointer hover:bg-slate-50 transition-colors file:mr-4 file:py-2.5 file:px-4 file:border-0 file:border-r file:border-slate-200 file:bg-slate-50 file:text-slate-700 file:text-sm file:font-semibold">
            </div>

            <div class="shrink-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Berita</label>
                <input type="text" name="title" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-1 focus:ring-blue-600 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4 shrink-0">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Level Urgensi</label>
                    <select name="urgency_level" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Pilih level</option>
                        <option value="Berita Utama">Berita Utama</option>
                        <option value="Prakiraan Cuaca">Prakiraan Cuaca</option>
                        <option value="Peringatan Dini">Peringatan Dini</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="news_date" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-1 focus:ring-blue-600 outline-none">
                </div>
            </div>

            <div class="flex-1 flex flex-col min-h-0">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Detail Konten</label>
                <textarea name="content" required class="flex-1 w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-1 focus:ring-blue-600 outline-none resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 shrink-0">
                <a href="{{ route('admin.news.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#1e3a8a] text-white text-sm font-semibold hover:bg-blue-800 shadow-sm transition-colors cursor-pointer">Simpan Berita</button>
            </div>
        </form>
    </div>
</div>
@endsection