@extends('layouts.app')

@section('title', 'Edit Pembaruan Aplikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('superadmin.update.index') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1.5 font-medium">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali ke Riwayat Update</span>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold">Edit Pembaruan v{{ $update->version }}</span>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-r from-emerald-800 to-teal-800 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-emerald-300 text-xl border border-white/20">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">Edit Rilis Pembaruan Aplikasi</h1>
                        <p class="text-xs text-emerald-100/80 mt-0.5">{{ $update->name }} &bull; Versi {{ $update->version }} ({{ strtoupper($update->platform) }})</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-white/20 text-white border border-white/30 uppercase">
                    {{ $update->platform }}
                </span>
            </div>
        </div>

        <!-- Form Body -->
        <form action="{{ route('superadmin.update.update', $update->id) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Platform Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Platform Target <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Android Card -->
                    <div id="card-platform-android" onclick="selectPlatform('android')" 
                         class="platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-xl shrink-0">
                                <i class="fab fa-android"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 leading-tight">Android</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Google Play Store / APK</p>
                            </div>
                        </div>
                        <input type="radio" name="platform" id="platform_android" value="android" 
                               {{ old('platform', $update->platform) === 'android' ? 'checked' : '' }} 
                               onchange="selectPlatform('android')"
                               class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500 cursor-pointer">
                    </div>

                    <!-- iOS Card -->
                    <div id="card-platform-ios" onclick="selectPlatform('ios')" 
                         class="platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">
                                <i class="fab fa-apple"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 leading-tight">iOS</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Apple App Store</p>
                            </div>
                        </div>
                        <input type="radio" name="platform" id="platform_ios" value="ios" 
                               {{ old('platform', $update->platform) === 'ios' ? 'checked' : '' }} 
                               onchange="selectPlatform('ios')"
                               class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                    </div>

                    <!-- Web Card -->
                    <div id="card-platform-web" onclick="selectPlatform('web')" 
                         class="platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all select-none">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl shrink-0">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 leading-tight">Website</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Realtime Web Patch</p>
                            </div>
                        </div>
                        <input type="radio" name="platform" id="platform_web" value="web" 
                               {{ old('platform', $update->platform) === 'web' ? 'checked' : '' }} 
                               onchange="selectPlatform('web')"
                               class="w-4 h-4 text-teal-600 border-slate-300 focus:ring-teal-500 cursor-pointer">
                    </div>
                </div>
                @error('platform')
                    <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Version & Title Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama / Judul Rilis <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $update->name) }}" placeholder="cth: Aku Sehat Mobile v1.2.0"
                           class="w-full text-sm rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-rose-300 bg-rose-50/30 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="version" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Versi <span class="text-rose-500">*</span></label>
                        <input type="text" name="version" id="version" required value="{{ old('version', $update->version) }}" placeholder="cth: 1.2.0"
                               class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('version') border-rose-300 bg-rose-50/30 @enderror">
                        @error('version')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="min_version" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Min Versi (Opsi)</label>
                        <input type="text" name="min_version" id="min_version" value="{{ old('min_version', $update->min_version) }}" placeholder="cth: 1.0.0"
                               class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Link URL & Developer -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="link" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>
                            <span id="linkLabel">Link Rilis / URL Toko</span>
                            <span id="linkRequiredStar" class="text-rose-500">*</span>
                        </span>
                        <span id="linkOptionalBadge" class="text-[11px] text-slate-400 font-normal lowercase hidden">(opsional)</span>
                    </label>
                    <input type="text" name="link" id="link" value="{{ old('link', $update->link) }}" placeholder="https://play.google.com/..."
                           class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('link') border-rose-300 bg-rose-50/30 @enderror">
                    <p id="linkHint" class="text-[11px] text-slate-400 mt-1">Tautan rilis ke Play Store, App Store, atau domain web.</p>
                    @error('link')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="developer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pengembang / Tim Rilis <span class="text-rose-500">*</span></label>
                    <input type="text" name="developer" id="developer" required value="{{ old('developer', $update->developer) }}"
                           class="w-full text-sm rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <!-- Date & Status Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="release_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Rilis <span class="text-rose-500">*</span></label>
                    <input type="date" name="release_date" id="release_date" required value="{{ old('release_date', $update->release_date ? $update->release_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                           class="w-full text-sm rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="active" {{ old('status', $update->status) === 'active' ? 'selected' : '' }}>Aktif (Dipublikasikan ke Pengguna)</option>
                        <option value="inactive" {{ old('status', $update->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif / Draft</option>
                    </select>
                </div>
            </div>

            <!-- Is Mandatory Toggle for Mobile -->
            <div id="mandatoryUpdateWrapper" class="p-4 rounded-xl bg-amber-50 border border-amber-200 transition-opacity">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_mandatory" value="1" {{ old('is_mandatory', $update->is_mandatory) ? 'checked' : '' }} class="mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-amber-900 block">Wajibkan Pembaruan (Force / Mandatory Update)</span>
                        <span class="text-[11px] text-amber-700 leading-relaxed block mt-0.5">
                            Jika dicentang, pengguna aplikasi mobile dengan versi di bawah ini akan diwajibkan melakukan update di Play Store sebelum dapat menggunakan aplikasi.
                        </span>
                    </div>
                </label>
            </div>

            <!-- Short Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat Rilis</label>
                <input type="text" name="description" id="description" value="{{ old('description', $update->description) }}" placeholder="cth: Pembaruan fitur konseling, perbaikan bug sinkronisasi"
                       class="w-full text-sm rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Detailed Changelog / Patch Notes with GitHub Markdown Live Preview -->
            <div class="space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <label for="changelog" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Perubahan Lengkap (Patch Notes / Changelog)
                    </label>

                    <!-- Tabs: Tulis & Pratinjau Markdown -->
                    <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs self-start sm:self-auto">
                        <button type="button" id="tab-changelog-write" onclick="switchChangelogTab('write')"
                                class="px-3 py-1 font-semibold rounded-lg transition-all bg-white text-emerald-800 shadow-xs flex items-center gap-1.5">
                            <i class="fas fa-pen-to-square text-[11px]"></i>
                            <span>Tulis Markdown</span>
                        </button>
                        <button type="button" id="tab-changelog-preview" onclick="switchChangelogTab('preview')"
                                class="px-3 py-1 font-semibold rounded-lg transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                            <i class="fab fa-markdown text-slate-700 text-sm"></i>
                            <span>Pratinjau Markdown</span>
                        </button>
                    </div>
                </div>

                <!-- Write Area -->
                <div id="changelog-write-pane">
                    <textarea name="changelog" id="changelog" rows="7" spellcheck="false" autocomplete="off" autocapitalize="off"
                              placeholder="### Apa yang Baru di Versi ini:&#10;* **Fitur Utama**:&#10;  - Penambahan visual metrik status kesehatan&#10;* **Perbaikan Bug**:&#10;  - Perbaikan crash saat klik tombol Detail"
                              class="w-full text-sm rounded-xl border border-slate-300 p-3 font-mono text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 leading-relaxed">{{ old('changelog', $update->changelog) }}</textarea>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-slate-400 mt-1">
                        <span>Format teks mendukung Markdown standar & HTML (<code>### Judul</code>, <code>**tebal**</code>, <code>- List</code>, <code>![gambar](url)</code>, <code>&lt;video&gt;</code>, <code>`kode`</code>).</span>
                        <button type="button" onclick="insertMarkdownSample()" class="text-emerald-700 hover:text-emerald-800 font-semibold inline-flex items-center gap-1 self-start sm:self-auto">
                            <i class="fas fa-wand-magic-sparkles text-[10px]"></i>
                            <span>Isi Contoh Format</span>
                        </button>
                    </div>
                </div>

                <!-- Markdown Preview Area -->
                <div id="changelog-preview-pane" class="hidden">
                    <div class="rounded-xl border border-slate-300 bg-white overflow-hidden shadow-inner">
                        <div class="px-4 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-[11px] text-slate-500">
                            <span class="font-semibold flex items-center gap-1.5">
                                <i class="fab fa-markdown text-slate-800 text-sm"></i>
                                Tampilan Pratinjau Markdown
                            </span>
                            <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-medium">
                                Render Otomatis
                            </span>
                        </div>
                        <div id="changelog-preview-body" class="p-4 min-h-[160px] max-h-[420px] overflow-y-auto github-markdown-body text-xs text-slate-800 leading-relaxed bg-white">
                            <!-- Live Parsed Markdown rendered here -->
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Tampilan ini identik dengan apa yang akan dibaca pengguna di halaman Riwayat Patch dan aplikasi mobile.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('superadmin.update.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 border border-emerald-800 shadow-md shadow-emerald-900/15 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function selectPlatform(platform) {
        const radio = document.getElementById('platform_' + platform);
        if (radio) {
            radio.checked = true;
        }

        const cards = {
            android: document.getElementById('card-platform-android'),
            ios: document.getElementById('card-platform-ios'),
            web: document.getElementById('card-platform-web')
        };

        const defaultClass = 'platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:bg-slate-50 transition-all select-none';

        Object.keys(cards).forEach(key => {
            const card = cards[key];
            if (card) {
                card.className = defaultClass;
            }
        });

        const activeCard = cards[platform];
        if (activeCard) {
            if (platform === 'android') {
                activeCard.className = 'platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-emerald-600 bg-emerald-50/70 shadow-sm ring-2 ring-emerald-500/20 cursor-pointer transition-all select-none';
            } else if (platform === 'ios') {
                activeCard.className = 'platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/70 shadow-sm ring-2 ring-blue-500/20 cursor-pointer transition-all select-none';
            } else if (platform === 'web') {
                activeCard.className = 'platform-card relative flex items-center justify-between p-4 rounded-2xl border-2 border-teal-600 bg-teal-50/70 shadow-sm ring-2 ring-teal-500/20 cursor-pointer transition-all select-none';
            }
        }

        const mandatoryWrapper = document.getElementById('mandatoryUpdateWrapper');
        const linkInput = document.getElementById('link');
        const linkLabel = document.getElementById('linkLabel');
        const linkRequiredStar = document.getElementById('linkRequiredStar');
        const linkOptionalBadge = document.getElementById('linkOptionalBadge');
        const linkHint = document.getElementById('linkHint');

        if (platform === 'web') {
            if (mandatoryWrapper) mandatoryWrapper.classList.add('opacity-40');
            if (linkInput) {
                linkInput.removeAttribute('required');
                linkInput.placeholder = 'https://akusehat.id atau kosongkan jika domain sama';
            }
            if (linkLabel) linkLabel.innerText = 'Tautan Web / Domain Rilis';
            if (linkRequiredStar) linkRequiredStar.classList.add('hidden');
            if (linkOptionalBadge) linkOptionalBadge.classList.remove('hidden');
            if (linkHint) linkHint.innerText = 'Opsional: Kosongkan jika menggunakan domain saat ini, atau isi jika ada custom domain/link landing rilis.';
        } else {
            if (mandatoryWrapper) mandatoryWrapper.classList.remove('opacity-40');
            if (linkInput) {
                linkInput.setAttribute('required', 'required');
                linkInput.placeholder = platform === 'android' 
                    ? 'https://play.google.com/store/apps/details?id=...' 
                    : 'https://apps.apple.com/id/app/...';
            }
            if (linkLabel) linkLabel.innerText = platform === 'android' ? 'Link Google Play Store' : 'Link Apple App Store';
            if (linkRequiredStar) linkRequiredStar.classList.remove('hidden');
            if (linkOptionalBadge) linkOptionalBadge.classList.add('hidden');
            if (linkHint) linkHint.innerText = platform === 'android' 
                ? 'Tautan aplikasi di Google Play Store atau link download APK rilis.' 
                : 'Tautan aplikasi di Apple App Store.';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const currentChecked = document.querySelector('input[name="platform"]:checked');
        if (currentChecked) {
            selectPlatform(currentChecked.value);
        } else {
            selectPlatform('{{ $update->platform ?? "android" }}');
        }
    });

    // --- Changelog Markdown Preview & Tabs ---
    function switchChangelogTab(tab) {
        const writeBtn = document.getElementById('tab-changelog-write');
        const previewBtn = document.getElementById('tab-changelog-preview');
        const writePane = document.getElementById('changelog-write-pane');
        const previewPane = document.getElementById('changelog-preview-pane');
        const previewBody = document.getElementById('changelog-preview-body');
        const textarea = document.getElementById('changelog');

        if (tab === 'preview') {
            writeBtn.className = 'px-3 py-1 font-semibold rounded-lg transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
            previewBtn.className = 'px-3 py-1 font-semibold rounded-lg transition-all bg-white text-emerald-800 shadow-xs flex items-center gap-1.5';
            writePane.classList.add('hidden');
            previewPane.classList.remove('hidden');

            const content = textarea ? textarea.value : '';
            previewBody.innerHTML = parseMarkdownToHtml(content);
        } else {
            writeBtn.className = 'px-3 py-1 font-semibold rounded-lg transition-all bg-white text-emerald-800 shadow-xs flex items-center gap-1.5';
            previewBtn.className = 'px-3 py-1 font-semibold rounded-lg transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
            previewPane.classList.add('hidden');
            writePane.classList.remove('hidden');
            if (textarea) textarea.focus();
        }
    }

    function parseMarkdownToHtml(raw) {
        if (!raw || !raw.trim()) {
            return '<p class="text-slate-400 italic">Belum ada catatan yang ditulis. Kembali ke tab Tulis Markdown untuk menambahkan catatan.</p>';
        }

        let text = raw;

        // 1. Process Fenced code blocks ```lang ... ``` first before escaping
        const codeBlocks = [];
        text = text.replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/g, function(match, lang, code) {
            const placeholder = '___CODEBLOCK_' + codeBlocks.length + '___';
            const escapedCode = code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            codeBlocks.push('<pre class="bg-slate-900 text-slate-100 p-3 rounded-xl overflow-x-auto text-xs my-2 font-mono"><code>' + escapedCode.trim() + '</code></pre>');
            return placeholder;
        });

        // 2. Allow safe HTML tags (img, video, source, a, table, etc.) while escaping dangerous scripts
        text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        text = text.replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, function(match) {
            if (match.includes('youtube.com') || match.includes('youtu.be') || match.includes('vimeo.com')) {
                return match;
            }
            return '';
        });

        // 3. Markdown Images: ![alt](url)
        text = text.replace(/!\[([^\]]*)\]\((https?:\/\/[^\s)]+)\)/g, function(match, alt, url) {
            return '<div class="my-2"><img src="' + url + '" alt="' + alt + '" loading="lazy" class="rounded-xl max-w-full h-auto border border-slate-200 shadow-sm" /><p class="text-[10px] text-slate-400 mt-0.5 italic">' + (alt || 'Gambar') + '</p></div>';
        });

        // 4. YouTube links auto-embed: [video](https://www.youtube.com/watch?v=xyz) or direct youtube url
        text = text.replace(/\[(?:video|tonton|youtube)\]\((?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)\)/gi, function(match, id) {
            return '<div class="my-3 w-full aspect-video rounded-xl overflow-hidden shadow-sm border border-slate-200"><iframe class="w-full h-full" src="https://www.youtube.com/embed/' + id + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
        });

        // 5. Direct video files: [video](url.mp4) or direct mp4/webm markdown links
        text = text.replace(/\[([^\]]*)\]\((https?:\/\/[^\s)]+\.(?:mp4|webm|ogg))\)/gi, function(match, title, url) {
            return '<div class="my-3"><video controls class="w-full max-h-72 rounded-xl bg-slate-900 border border-slate-200 shadow-sm"><source src="' + url + '" type="video/mp4">Browser Anda tidak mendukung tag video.</video><p class="text-[10px] text-slate-400 mt-0.5">' + (title || 'Video Pembaruan') + '</p></div>';
        });

        // 6. Markdown standard links: [text](url)
        text = text.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-emerald-700 underline font-semibold hover:text-emerald-800">$1</a>');

        // 7. Headers
        text = text.replace(/^#### (.*?)$/gm, '<h4 class="font-bold text-slate-900 mt-2 mb-1 text-xs">$1</h4>');
        text = text.replace(/^### (.*?)$/gm, '<h3 class="font-bold text-slate-900 mt-3 mb-1 text-sm">$1</h3>');
        text = text.replace(/^## (.*?)$/gm, '<h2 class="font-bold text-slate-900 mt-4 mb-2 text-base pb-1 border-b border-slate-200">$1</h2>');
        text = text.replace(/^# (.*?)$/gm, '<h1 class="font-extrabold text-slate-900 mt-4 mb-2 text-lg pb-1.5 border-b border-slate-200">$1</h1>');

        // 8. Blockquotes
        text = text.replace(/^> (.*?)$/gm, '<blockquote class="border-l-4 border-emerald-500 bg-emerald-50/70 text-slate-700 px-3 py-1.5 rounded-r-lg my-2 italic text-xs">$1</blockquote>');

        // 9. Horizontal Rule
        text = text.replace(/^---$/gm, '<hr class="my-3 border-t border-slate-200">');

        // 10. Inline formatting (bold, italic, inline code)
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/__(.*?)__/g, '<strong>$1</strong>');
        text = text.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-emerald-800 px-1.5 py-0.5 rounded font-mono text-[11px] border border-slate-200">$1</code>');
        text = text.replace(/(^|[^*])\*(?!\*)(.*?)\*(?!\*)/g, '$1<em>$2</em>');

        // 11. Lists & Paragraphs
        const lines = text.split('\n');
        let html = '';
        let inList = false;
        let listType = '';

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const trimmed = line.trim();

            const bulletMatch = line.match(/^(\s*)([-*•])\s+(.*)$/);
            const numMatch = line.match(/^(\s*)(\d+)\.\s+(.*)$/);

            if (bulletMatch) {
                if (!inList || listType !== 'ul') {
                    if (inList) html += '</' + listType + '>';
                    html += '<ul class="list-disc pl-5 my-1.5 space-y-0.5 text-slate-700">';
                    inList = true;
                    listType = 'ul';
                }
                html += '<li>' + bulletMatch[3] + '</li>';
            } else if (numMatch) {
                if (!inList || listType !== 'ol') {
                    if (inList) html += '</' + listType + '>';
                    html += '<ol class="list-decimal pl-5 my-1.5 space-y-0.5 text-slate-700">';
                    inList = true;
                    listType = 'ol';
                }
                html += '<li>' + numMatch[3] + '</li>';
            } else {
                if (inList) {
                    html += '</' + listType + '>';
                    inList = false;
                    listType = '';
                }

                if (!trimmed) continue;

                if (trimmed.startsWith('<h') || trimmed.startsWith('<blockquote') || trimmed.startsWith('<pre') || trimmed.startsWith('<hr') || trimmed.startsWith('<div') || trimmed.startsWith('<img') || trimmed.startsWith('<video') || trimmed.startsWith('<table')) {
                    html += trimmed;
                } else if (trimmed.startsWith('___CODEBLOCK_')) {
                    html += trimmed;
                } else {
                    html += '<p class="my-1 text-slate-700 leading-relaxed">' + trimmed + '</p>';
                }
            }
        }

        if (inList) {
            html += '</' + listType + '>';
        }

        // 12. Restore code blocks
        codeBlocks.forEach(function(blockHtml, idx) {
            html = html.replace('___CODEBLOCK_' + idx + '___', blockHtml);
        });

        return html;
    }

    function insertMarkdownSample() {
        const textarea = document.getElementById('changelog');
        if (!textarea) return;
        const sample = `### Apa yang Baru di Versi ini:
* **Fitur Utama**:
  - Penambahan visual metrik status kesehatan
  - Peningkatan antarmuka profil dan portal konselor
* **Perbaikan Bug**:
  - Resolusi masalah saat klik tombol Detail pada divisi
  - Peningkatan kestabilan sinkronisasi data rekam medis
* **Optimalisasi Sistem**:
  - Peningkatan kecepatan pemuatan halaman dan efisiensi memori`;

        if (!textarea.value.trim() || confirm('Ganti isi catatan saat ini dengan template contoh format Markdown?')) {
            textarea.value = sample;
            textarea.focus();
        }
    }
</script>
@endpush
@endsection
