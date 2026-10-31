@extends('layouts.app')

@section('title', 'Web Terminal & Konsol Laravel')

@section('content')
<div class="space-y-3">
    <!-- Header Section (Compact) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-900 text-emerald-400 border border-slate-700">
                    <i class="fas fa-terminal mr-1"></i> Web Terminal
                </span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <i class="fas fa-shield-halved mr-1"></i> SuperAdmin
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
                Web Terminal & Konsol Laravel
            </h1>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <!-- Terminal Size Toggle -->
            <button type="button" 
                    onclick="toggleTerminalSize()"
                    id="size-toggle-btn"
                    title="Ubah Ukuran Layar Terminal"
                    class="btn-press inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <i class="fas fa-arrows-up-down text-slate-400" id="size-toggle-icon"></i>
                <span id="size-toggle-text">Mode Luas</span>
            </button>
            <button type="button" 
                    onclick="clearTerminal()"
                    class="btn-press inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <i class="fas fa-trash-can text-slate-400"></i>
                <span>Bersihkan</span>
            </button>
            <button type="button" 
                    onclick="copyTerminalLog()"
                    id="copy-log-btn"
                    class="btn-press inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all">
                <i class="fas fa-copy"></i>
                <span>Salin</span>
            </button>
        </div>
    </div>

    <!-- Unified Compact Specs & Quick Presets Bar -->
    <div class="p-2.5 sm:p-3 rounded-2xl bg-white border border-slate-200/90 shadow-soft space-y-2">
        <!-- Top Row: Specs Chips -->
        <div class="flex items-center justify-between flex-wrap gap-2 text-xs border-b border-slate-100 pb-2">
            <div class="flex items-center flex-wrap gap-2 text-slate-600 font-mono text-[11px]">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-bold border border-indigo-100">
                    <i class="fab fa-php text-xs"></i> PHP {{ $systemInfo['php_version'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-red-700 font-bold border border-red-100">
                    <i class="fab fa-laravel text-xs"></i> Laravel {{ $systemInfo['laravel_version'] }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium border border-slate-200" title="{{ $systemInfo['os'] }}">
                    <i class="fas fa-server text-[10px]"></i> {{ Str::limit($systemInfo['os'], 25) }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Konsol Siap</span>
            </div>
        </div>

        <!-- Bottom Row: Quick Preset Buttons (Laravel, Linux & Server) -->
        <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 scrollbar-thin">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 shrink-0 mr-1 flex items-center gap-1">
                <i class="fas fa-bolt text-amber-500 text-[10px]"></i> Pintasan:
            </span>
            <button type="button" onclick="runPresetCommand('ls -la')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition-colors">
                <i class="fas fa-folder-tree text-[9px] mr-1"></i>ls -la
            </button>
            <button type="button" onclick="runPresetCommand('pwd')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                pwd
            </button>
            <button type="button" onclick="runPresetCommand('cat .env.example')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                cat .env.example
            </button>
            <button type="button" onclick="runPresetCommand('nano .env')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 transition-colors">
                <i class="fas fa-edit text-[9px] mr-1"></i>nano .env
            </button>
            <button type="button" onclick="runPresetCommand('php artisan optimize:clear')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                optimize:clear
            </button>
            <button type="button" onclick="runPresetCommand('composer dump-autoload')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition-colors">
                composer dump-autoload
            </button>
            <button type="button" onclick="runPresetCommand('composer -v')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                composer -v
            </button>
            <button type="button" onclick="runPresetCommand('php -v')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                php -v
            </button>
            <button type="button" onclick="runPresetCommand('df -h')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                df -h
            </button>
            <button type="button" onclick="runPresetCommand('git status')" class="btn-press shrink-0 px-2 py-1 rounded-lg text-[11px] font-mono font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 transition-colors">
                git status
            </button>
        </div>
    </div>

    <!-- Main Terminal Console Window (Standard Compact Height) -->
    <div id="terminal-card" class="rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-xl transition-all duration-200">
        <!-- Terminal Window Titlebar (macOS Style, Compact) -->
        <div class="flex items-center justify-between px-3.5 sm:px-4 py-2 bg-slate-900 border-b border-slate-800 select-none">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                </div>
                <div class="h-3.5 w-px bg-slate-800 mx-1"></div>
                <div class="flex items-center gap-1.5 text-xs font-mono text-slate-300">
                    <i class="fas fa-terminal text-emerald-400 text-[10px]"></i>
                    <span class="font-bold text-white text-[11px]">superadmin@akusehat</span>
                    <span class="text-slate-500">:</span>
                    <span class="text-emerald-300 truncate max-w-xs sm:max-w-md text-[11px]">~ ({{ $systemInfo['base_path'] }})</span>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="hidden sm:inline font-mono text-[10px] text-slate-400">
                    <kbd class="px-1 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">Enter</kbd> Eksekusi &bull; 
                    <kbd class="px-1 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">&uarr;&darr;</kbd> Riwayat
                </span>
            </div>
        </div>

        <!-- Terminal Screen Output Area (Standard Ergonomic Height: 260px) -->
        <div id="terminal-screen" 
             class="p-3 sm:p-4 font-mono text-xs text-slate-100 overflow-y-auto h-[260px] space-y-3 select-text leading-relaxed transition-all duration-200">
            
            <!-- Compact Initial Welcome Banner -->
            <div class="text-slate-400 text-xs border-b border-slate-800/80 pb-2 flex items-center justify-between flex-wrap gap-1 select-none">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-400 font-bold"><i class="fas fa-terminal mr-1"></i> AkuSehat Terminal</span>
                    <span class="text-slate-600">&bull;</span>
                    <span class="text-slate-300">Laravel {{ $systemInfo['laravel_version'] }} / PHP {{ $systemInfo['php_version'] }}</span>
                </div>
                <span class="text-slate-500 text-[11px]">Dukungan: php artisan ..., composer ..., ls, cat, nano, rm, cp, mv, git, dll.</span>
            </div>

            <!-- Dynamic Outputs Will Be Appended Here -->
            <div id="terminal-history" class="space-y-3"></div>

            <!-- Running Spinner Indicator -->
            <div id="terminal-spinner" class="hidden items-center gap-2.5 py-1 text-emerald-400">
                <i class="fas fa-circle-notch fa-spin text-sm"></i>
                <span class="text-xs text-slate-300 font-mono">Sedang mengeksekusi perintah...</span>
            </div>
        </div>

        <!-- Terminal Command Input Bar (Compact & Always in View) -->
        <form id="terminal-form" onsubmit="handleCommandSubmit(event)" class="border-t border-slate-800 bg-slate-900/90 p-2 sm:p-2.5">
            <div class="flex items-center gap-2 bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                <div class="flex items-center gap-1 text-xs font-mono text-emerald-400 shrink-0 select-none">
                    <span class="font-bold">superadmin@akusehat</span>
                    <span class="text-slate-500">:</span>
                    <span class="text-cyan-400">~$</span>
                </div>
                
                <input type="text" 
                       id="terminal-input"
                       name="command"
                       placeholder="Masukkan perintah..."
                       autocomplete="off"
                       autocorrect="off"
                       autocapitalize="off"
                       spellcheck="false"
                       class="flex-1 bg-transparent text-white font-mono text-xs sm:text-sm placeholder-slate-500 focus:outline-none border-none p-0">

                <button type="submit" 
                        id="terminal-submit-btn"
                        title="Eksekusi Perintah (Enter)"
                        class="btn-press flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all shrink-0">
                    <i class="fas fa-play text-[9px]"></i>
                    <span class="hidden sm:inline">Jalankan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- GNU nano In-Browser File Editor Modal -->
    <div id="nano-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-3 sm:p-6 transition-all duration-200">
        <div class="relative w-full max-w-4xl bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
            <!-- Modal Titlebar (Classic Nano Header) -->
            <div class="flex items-center justify-between px-4 py-2.5 bg-slate-950 border-b border-slate-800 select-none">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <div class="h-4 w-px bg-slate-800 mx-1"></div>
                    <span class="text-xs font-mono font-bold text-emerald-400">GNU nano (Web Editor)</span>
                    <span class="text-slate-600 font-mono">|</span>
                    <span class="text-xs font-mono text-white font-bold truncate max-w-xs" id="nano-filename">.env</span>
                    <span id="nano-dirty-badge" class="hidden text-[10px] font-mono px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Dimodifikasi</span>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-[11px] font-mono text-slate-400 hidden sm:inline" id="nano-line-stats">Baris: 0</span>
                    <button type="button" 
                            onclick="closeNanoModal()" 
                            title="Tutup Editor (^X / Esc)"
                            class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Editor Body -->
            <div class="flex-1 overflow-hidden p-2 sm:p-3 bg-slate-950 flex flex-col">
                <textarea id="nano-editor-textarea"
                          spellcheck="false"
                          autocomplete="off"
                          autocorrect="off"
                          autocapitalize="off"
                          class="w-full flex-1 min-h-[320px] sm:min-h-[420px] font-mono text-xs sm:text-sm text-slate-100 bg-slate-950 border border-slate-800 rounded-xl p-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 leading-relaxed resize-none scrollbar-thin select-text"></textarea>
            </div>

            <!-- Classic Nano Bottom Shortcut Toolbar -->
            <div class="px-3 sm:px-4 py-2 bg-slate-900 border-t border-slate-800 flex items-center justify-between flex-wrap gap-2 text-xs font-mono">
                <!-- Status Msg -->
                <div id="nano-status-msg" class="text-[11px] text-slate-400 truncate max-w-sm sm:max-w-md">
                    Tekan <kbd class="px-1 py-0.5 rounded bg-slate-800 text-emerald-400 font-bold border border-slate-700">Ctrl+S</kbd> untuk Simpan, <kbd class="px-1 py-0.5 rounded bg-slate-800 text-slate-300 font-bold border border-slate-700">Esc</kbd> untuk Keluar.
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="closeNanoModal()"
                            class="btn-press px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors flex items-center gap-1.5">
                        <span class="text-amber-400 font-bold">^X</span>
                        <span>Keluar</span>
                    </button>
                    <button type="button" 
                            id="nano-save-btn"
                            onclick="saveNanoFile()"
                            class="btn-press px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all flex items-center gap-1.5">
                        <span class="text-white font-bold">^O</span>
                        <span id="nano-save-btn-text">Simpan (WriteOut)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const terminalHistory = [];
    let historyIndex = -1;
    let fullOutputText = "";

    const terminalInput = document.getElementById('terminal-input');
    const terminalHistoryContainer = document.getElementById('terminal-history');
    const terminalScreen = document.getElementById('terminal-screen');
    const terminalSpinner = document.getElementById('terminal-spinner');
    const terminalSubmitBtn = document.getElementById('terminal-submit-btn');

    // Auto-focus input on page load
    document.addEventListener('DOMContentLoaded', () => {
        if (terminalInput) terminalInput.focus();
    });

    // Handle Keyboard navigation for Command History (Up / Down)
    terminalInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (terminalHistory.length === 0) return;
            if (historyIndex === -1) {
                historyIndex = terminalHistory.length - 1;
            } else if (historyIndex > 0) {
                historyIndex--;
            }
            terminalInput.value = terminalHistory[historyIndex] || '';
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (terminalHistory.length === 0 || historyIndex === -1) return;
            if (historyIndex < terminalHistory.length - 1) {
                historyIndex++;
                terminalInput.value = terminalHistory[historyIndex];
            } else {
                historyIndex = -1;
                terminalInput.value = '';
            }
        } else if (e.ctrlKey && e.key.toLowerCase() === 'l') {
            e.preventDefault();
            clearTerminal();
        }
    });

    // Run preset command from quick buttons
    window.runPresetCommand = function(cmd) {
        if (!terminalInput) return;
        terminalInput.value = cmd;
        terminalInput.focus();
        document.getElementById('terminal-form').dispatchEvent(new Event('submit', { cancelable: true }));
    };

    // Toggle terminal height between Standard (260px) and Expanded (480px)
    let isExpanded = false;
    window.toggleTerminalSize = function() {
        isExpanded = !isExpanded;
        const screen = document.getElementById('terminal-screen');
        const icon = document.getElementById('size-toggle-icon');
        const text = document.getElementById('size-toggle-text');

        if (!screen) return;

        if (isExpanded) {
            screen.classList.remove('h-[260px]');
            screen.classList.add('h-[480px]');
            if (icon) icon.className = 'fas fa-compress text-slate-400';
            if (text) text.textContent = 'Ukuran Standar';
        } else {
            screen.classList.remove('h-[480px]');
            screen.classList.add('h-[260px]');
            if (icon) icon.className = 'fas fa-arrows-up-down text-slate-400';
            if (text) text.textContent = 'Mode Luas';
        }
        try {
            localStorage.setItem('terminal_is_expanded', isExpanded ? '1' : '0');
        } catch (e) {}
        screen.scrollTop = screen.scrollHeight;
    };

    // Restore saved terminal size preference if previously set
    (function() {
        try {
            if (localStorage.getItem('terminal_is_expanded') === '1') {
                toggleTerminalSize();
            }
        } catch (e) {}
    })();

    // Clear terminal screen
    window.clearTerminal = function() {
        if (terminalHistoryContainer) {
            terminalHistoryContainer.innerHTML = '';
        }
        fullOutputText = "";
        if (terminalInput) terminalInput.focus();
    };

    // Copy terminal output to clipboard
    window.copyTerminalLog = function() {
        const textToCopy = fullOutputText || "Belum ada output perintah pada sesi terminal ini.";
        navigator.clipboard.writeText(textToCopy).then(() => {
            const btn = document.getElementById('copy-log-btn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> <span>Tersalin!</span>';
            btn.classList.add('bg-teal-600');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-teal-600');
            }, 2000);
        }).catch(() => {
            alert('Gagal menyalin log terminal.');
        });
    };

    // GNU nano Web Editor Logic
    let currentNanoFile = "";
    const nanoModal = document.getElementById('nano-modal');
    const nanoTextarea = document.getElementById('nano-editor-textarea');
    const nanoFilename = document.getElementById('nano-filename');
    const nanoDirtyBadge = document.getElementById('nano-dirty-badge');
    const nanoLineStats = document.getElementById('nano-line-stats');
    const nanoStatusMsg = document.getElementById('nano-status-msg');
    const nanoSaveBtn = document.getElementById('nano-save-btn');
    const nanoSaveBtnText = document.getElementById('nano-save-btn-text');

    window.openNanoModal = function(data) {
        currentNanoFile = data.file;
        nanoFilename.textContent = data.file;
        nanoTextarea.value = data.content || '';
        nanoDirtyBadge.classList.add('hidden');
        updateNanoLineStats();
        nanoStatusMsg.innerHTML = data.is_new 
            ? `<span class="text-emerald-400 font-bold">[ Berkas Baru: ${escapeHtml(data.file)} ]</span>`
            : `<span class="text-slate-300">Membaca ${data.lines || 0} baris dari ${escapeHtml(data.file)}</span>`;
        nanoModal.classList.remove('hidden');
        nanoModal.classList.add('flex');
        setTimeout(() => nanoTextarea.focus(), 100);
    };

    window.closeNanoModal = function() {
        if (!nanoModal.classList.contains('hidden')) {
            nanoModal.classList.add('hidden');
            nanoModal.classList.remove('flex');
            if (terminalInput) terminalInput.focus();
        }
    };

    function updateNanoLineStats() {
        const lines = nanoTextarea.value.split('\n').length;
        nanoLineStats.textContent = `Baris: ${lines} | Karakter: ${nanoTextarea.value.length}`;
    }

    nanoTextarea.addEventListener('input', () => {
        nanoDirtyBadge.classList.remove('hidden');
        updateNanoLineStats();
    });

    nanoTextarea.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = nanoTextarea.selectionStart;
            const end = nanoTextarea.selectionEnd;
            nanoTextarea.value = nanoTextarea.value.substring(0, start) + '    ' + nanoTextarea.value.substring(end);
            nanoTextarea.selectionStart = nanoTextarea.selectionEnd = start + 4;
            nanoDirtyBadge.classList.remove('hidden');
            updateNanoLineStats();
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            saveNanoFile();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeNanoModal();
        }
    });

    window.saveNanoFile = async function() {
        if (!currentNanoFile) return;
        const content = nanoTextarea.value;
        const originalText = nanoSaveBtnText.textContent;
        nanoSaveBtn.disabled = true;
        nanoSaveBtnText.textContent = "Menyimpan...";
        nanoStatusMsg.innerHTML = `<span class="text-amber-400"><i class="fas fa-circle-notch fa-spin mr-1"></i>Menyimpan berkas '${escapeHtml(currentNanoFile)}'...</span>`;

        try {
            const response = await fetch("{{ route('superadmin.terminal.save-file') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ file: currentNanoFile, content: content })
            });

            const res = await response.json();
            if (res.success) {
                nanoDirtyBadge.classList.add('hidden');
                nanoStatusMsg.innerHTML = `<span class="text-emerald-400 font-bold"><i class="fas fa-check mr-1"></i>${escapeHtml(res.message)}</span>`;
                nanoSaveBtnText.textContent = "Tersimpan!";
                setTimeout(() => {
                    nanoSaveBtnText.textContent = originalText;
                    nanoSaveBtn.disabled = false;
                }, 1500);

                // Append status entry to terminal history
                const entry = document.createElement('div');
                entry.className = 'border border-emerald-900/60 rounded-xl bg-emerald-950/40 p-2 sm:p-2.5 text-xs font-mono text-emerald-300';
                entry.innerHTML = `<i class="fas fa-floppy-disk text-emerald-400 mr-1.5"></i>[ nano: ${escapeHtml(res.message)} ]`;
                terminalHistoryContainer.appendChild(entry);
                terminalScreen.scrollTop = terminalScreen.scrollHeight;
            } else {
                nanoStatusMsg.innerHTML = `<span class="text-rose-400 font-bold"><i class="fas fa-triangle-exclamation mr-1"></i>${escapeHtml(res.message)}</span>`;
                nanoSaveBtnText.textContent = originalText;
                nanoSaveBtn.disabled = false;
            }
        } catch (err) {
            nanoStatusMsg.innerHTML = `<span class="text-rose-400 font-bold">Gagal menyimpan: ${escapeHtml(err.message)}</span>`;
            nanoSaveBtnText.textContent = originalText;
            nanoSaveBtn.disabled = false;
        }
    };

    // Confirmation for Destructive / Production Commands
    let pendingConfirmationCommand = null;

    window.confirmAndRunPendingCommand = function(cmd) {
        pendingConfirmationCommand = null;
        executeTerminalCommand(cmd, true);
    };

    window.cancelPendingCommand = function() {
        pendingConfirmationCommand = null;
        const cancelBlock = document.createElement('div');
        cancelBlock.className = 'border border-amber-900/60 rounded-xl bg-amber-950/40 p-2 sm:p-2.5 text-xs font-mono text-amber-300';
        cancelBlock.innerHTML = `<i class="fas fa-ban text-amber-400 mr-1.5"></i><span>Command Cancelled! (Perintah dibatalkan oleh pengguna, database tetap aman).</span>`;
        terminalHistoryContainer.appendChild(cancelBlock);
        terminalScreen.scrollTop = terminalScreen.scrollHeight;
        if (terminalInput) terminalInput.focus();
    };

    // Submit and execute command
    window.handleCommandSubmit = async function(event) {
        event.preventDefault();
        const command = terminalInput.value.trim();
        if (!command) return;

        // If a destructive/production command is waiting for confirmation
        if (pendingConfirmationCommand) {
            const lower = command.toLowerCase();
            if (['yes', 'y', 'ya', 'true', '1'].includes(lower)) {
                terminalInput.value = '';
                const cmdToRun = pendingConfirmationCommand;
                pendingConfirmationCommand = null;
                executeTerminalCommand(cmdToRun, true);
                return;
            } else if (['no', 'n', 'tidak', 'false', '0', 'cancel'].includes(lower)) {
                terminalInput.value = '';
                cancelPendingCommand();
                return;
            }
        }

        // Push to history
        terminalHistory.push(command);
        historyIndex = -1;
        terminalInput.value = '';

        executeTerminalCommand(command, false);
    };

    async function executeTerminalCommand(command, isConfirmed = false) {
        // Append Command Header Block to Terminal Screen (Compact)
        const entryBlock = document.createElement('div');
        entryBlock.className = 'border border-slate-800/90 rounded-xl bg-slate-900/60 p-2.5 sm:p-3 space-y-1.5 transition-all';
        
        const timestamp = new Date().toLocaleTimeString('id-ID');
        entryBlock.innerHTML = `
            <div class="flex items-center justify-between flex-wrap gap-1.5 pb-1.5 border-b border-slate-800/80">
                <div class="flex items-center gap-1.5">
                    <span class="text-emerald-400 font-bold text-xs select-none">superadmin@akusehat:~$</span>
                    <span class="text-white font-bold text-xs font-mono break-all">${escapeHtml(command)}${isConfirmed ? ' <span class="text-emerald-400 font-semibold">[Dikonfirmasi]</span>' : ''}</span>
                </div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono text-slate-400">
                    <span class="status-badge px-1.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold border border-slate-700 flex items-center gap-1">
                        <i class="fas fa-circle-notch fa-spin text-[8px] text-amber-400"></i>
                        <span>Memproses</span>
                    </span>
                    <span class="text-slate-500">${timestamp}</span>
                </div>
            </div>
            <div class="output-content font-mono text-xs text-slate-300 whitespace-pre-wrap leading-relaxed break-words">
                <span class="text-slate-500 italic">Menunggu respon server...</span>
            </div>
        `;
        terminalHistoryContainer.appendChild(entryBlock);

        // Show spinner & scroll to bottom
        terminalSpinner.classList.remove('hidden');
        terminalSpinner.classList.add('flex');
        terminalInput.disabled = true;
        terminalSubmitBtn.disabled = true;
        terminalScreen.scrollTop = terminalScreen.scrollHeight;

        try {
            const response = await fetch("{{ route('superadmin.terminal.execute') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ 
                    command: command,
                    confirmed: isConfirmed 
                })
            });

            const data = await response.json();
            const outputElement = entryBlock.querySelector('.output-content');
            const badgeElement = entryBlock.querySelector('.status-badge');

            // Handle special frontend actions
            if (data.action === 'clear_terminal') {
                clearTerminal();
                return;
            }

            if (data.action === 'open_nano') {
                openNanoModal(data);
            }

            // Handle Interactive Production / Destructive Confirmation Prompt
            if (data.requires_confirmation) {
                pendingConfirmationCommand = data.command;
                badgeElement.className = 'status-badge px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-semibold border border-amber-500/30 flex items-center gap-1';
                badgeElement.innerHTML = `<i class="fas fa-triangle-exclamation text-[9px]"></i> <span>Konfirmasi Diperlukan</span>`;

                outputElement.innerHTML = `
                    <div class="space-y-2.5">
                        <div class="text-amber-300 font-bold whitespace-pre-wrap leading-relaxed">${escapeHtml(data.output)}</div>
                        <div class="pt-2 flex items-center flex-wrap gap-2 border-t border-slate-800">
                            <button type="button" 
                                    onclick="confirmAndRunPendingCommand('${escapeHtml(data.command)}')"
                                    class="btn-press px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-check text-[10px]"></i>
                                <span>Yes, Lanjutkan (Run)</span>
                            </button>
                            <button type="button" 
                                    onclick="cancelPendingCommand()"
                                    class="btn-press px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-rose-300 font-semibold text-xs flex items-center gap-1.5 border border-slate-700">
                                <i class="fas fa-times text-[10px]"></i>
                                <span>No, Batalkan (Cancel)</span>
                            </button>
                            <span class="text-[11px] text-slate-400 ml-1 hidden sm:inline">(Atau ketik <b class="text-emerald-400 font-mono">yes</b> / <b class="text-rose-400 font-mono">no</b> di bawah)</span>
                        </div>
                    </div>
                `;
                return;
            }

            if (data.success) {
                badgeElement.className = 'status-badge px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-semibold border border-emerald-500/30 flex items-center gap-1';
                badgeElement.innerHTML = `<i class="fas fa-circle-check text-[9px]"></i> <span>Sukses (0) &bull; ${data.duration_ms}ms</span>`;
                outputElement.innerHTML = `<span class="text-slate-200">${escapeHtml(data.output)}</span>`;
            } else {
                badgeElement.className = 'status-badge px-1.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 font-semibold border border-rose-500/30 flex items-center gap-1';
                badgeElement.innerHTML = `<i class="fas fa-triangle-exclamation text-[9px]"></i> <span>Gagal (${data.exit_code}) &bull; ${data.duration_ms}ms</span>`;
                outputElement.innerHTML = `<span class="text-rose-300">${escapeHtml(data.output)}</span>`;
            }

            fullOutputText += `\n[${timestamp}] superadmin@akusehat:~$ ${command}\nExit Code: ${data.exit_code} (${data.duration_ms}ms)\n${data.output}\n`;

        } catch (error) {
            const outputElement = entryBlock.querySelector('.output-content');
            const badgeElement = entryBlock.querySelector('.status-badge');
            badgeElement.className = 'status-badge px-1.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 font-semibold border border-rose-500/30 flex items-center gap-1';
            badgeElement.innerHTML = `<i class="fas fa-triangle-exclamation text-[9px]"></i> <span>Error Koneksi</span>`;
            outputElement.innerHTML = `<span class="text-rose-400">Gagal berkomunikasi dengan server: ${escapeHtml(error.message || 'Unknown Network Error')}</span>`;
            fullOutputText += `\n[${timestamp}] superadmin@akusehat:~$ ${command}\nError: ${error.message}\n`;
        } finally {
            terminalSpinner.classList.add('hidden');
            terminalSpinner.classList.remove('flex');
            terminalInput.disabled = false;
            terminalSubmitBtn.disabled = false;
            terminalInput.focus();
            terminalScreen.scrollTop = terminalScreen.scrollHeight;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
