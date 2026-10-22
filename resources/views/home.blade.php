<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IT Knowledge Base</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js"></script>
<style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen" x-data="kb()" x-cloak>

<header class="bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
        <h1 class="font-bold text-lg whitespace-nowrap">IT Knowledge Base</h1>
        <input x-model="q" type="search" placeholder="Cari judul / isi..." class="flex-1 rounded px-3 py-2 text-slate-900 text-sm">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm bg-slate-700 hover:bg-slate-600 rounded px-3 py-2">Keluar</button>
        </form>
    </div>
</header>

@if($errors->any())
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-red-100 text-red-700 rounded p-3 text-sm">{{ $errors->first() }}</div>
</div>
@endif

<main class="max-w-7xl mx-auto px-4 py-6 grid md:grid-cols-4 gap-6">

    <aside class="md:col-span-1 bg-white rounded-lg shadow p-3 h-fit">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-sm">Kategori</span>
            <button @click="modal='cat'" class="text-xs bg-blue-600 text-white rounded px-2 py-1">+ Kategori</button>
        </div>
        <button @click="cat=null" :class="cat===null ? 'bg-blue-600 text-white' : 'hover:bg-slate-100'" class="w-full text-left px-3 py-2 rounded text-sm">Semua</button>
        <template x-for="m in mains" :key="m.id">
            <div>
                <button @click="cat=m.id" :class="cat===m.id ? 'bg-blue-600 text-white' : 'hover:bg-slate-100'" class="w-full text-left px-3 py-2 rounded text-sm font-medium" x-text="m.name"></button>
                <template x-for="c in childrenOf(m.id)" :key="c.id">
                    <button @click="cat=c.id" :class="cat===c.id ? 'bg-blue-600 text-white' : 'hover:bg-slate-100'" class="w-full text-left pl-8 pr-3 py-1.5 rounded text-sm" x-text="'↳ ' + c.name"></button>
                </template>
            </div>
        </template>
    </aside>

    <section class="md:col-span-3">
        <div class="flex items-center justify-between mb-4">
            <div class="flex gap-2">
                <button @click="tab='text'" :class="tab==='text' ? 'bg-blue-600 text-white' : 'bg-white'" class="px-4 py-2 rounded shadow text-sm">Catatan &amp; SOP</button>
                <button @click="tab='pdf'" :class="tab==='pdf' ? 'bg-blue-600 text-white' : 'bg-white'" class="px-4 py-2 rounded shadow text-sm">Dokumen PDF</button>
            </div>
            <button @click="modal='note'" class="bg-green-600 hover:bg-green-700 text-white rounded px-3 py-2 text-sm">+ Tambah</button>
        </div>

        <!-- TAB TEKS -->
        <div x-show="tab==='text'" class="space-y-4">
            <template x-for="n in list('text')" :key="n.id">
                <article class="bg-white rounded-lg shadow p-4">
                    <div class="flex justify-between gap-2">
                        <div>
                            <h3 class="font-semibold" x-text="n.title"></h3>
                            <p class="text-xs text-slate-500" x-text="catPath(n.category_id)"></p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button @click="copy(n)" class="text-xs bg-slate-800 text-white rounded px-3 py-1 h-fit" x-text="copied===n.id ? 'Tersalin ✓' : 'Copy'"></button>
                            <form method="POST" :action="'/notes/' + n.id" @submit="if(!confirm('Hapus catatan ini?')) $event.preventDefault()">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="text-xs bg-red-600 text-white rounded px-3 py-1">Hapus</button>
                            </form>
                        </div>
                    </div>
                    <pre class="mt-3 bg-slate-900 text-green-300 text-sm rounded p-3 overflow-x-auto whitespace-pre-wrap" x-text="n.content"></pre>
                </article>
            </template>
            <p x-show="list('text').length === 0" class="text-sm text-slate-500">Belum ada catatan.</p>
        </div>

        <!-- TAB PDF -->
        <div x-show="tab==='pdf'" class="grid lg:grid-cols-3 gap-4">
            <div class="space-y-2">
                <template x-for="n in list('pdf')" :key="n.id">
                    <div class="bg-white rounded-lg shadow p-3" :class="viewer && viewer.id===n.id ? 'ring-2 ring-blue-500' : ''">
                        <button @click="viewer=n" class="text-left w-full">
                            <div class="font-semibold text-sm" x-text="n.title"></div>
                            <div class="text-xs text-slate-500" x-text="catPath(n.category_id) + ' • ' + n.file_name"></div>
                        </button>
                        <div class="flex items-center gap-3 mt-2 text-xs">
                            <a :href="n.file_url" target="_blank" class="text-blue-600">Buka tab baru</a>
                            <form method="POST" :action="'/notes/' + n.id" @submit="if(!confirm('Hapus dokumen ini?')) $event.preventDefault()">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="text-red-600">Hapus</button>
                            </form>
                        </div>
                    </div>
                </template>
                <p x-show="list('pdf').length === 0" class="text-sm text-slate-500">Belum ada dokumen PDF.</p>
            </div>
            <div class="lg:col-span-2 bg-white rounded-lg shadow h-[80vh]">
                <template x-if="viewer">
                    <iframe :src="viewer.file_url" class="w-full h-full rounded-lg"></iframe>
                </template>
                <p x-show="!viewer" class="p-6 text-sm text-slate-500">Pilih dokumen untuk ditampilkan.</p>
            </div>
        </div>
    </section>
</main>

<!-- MODAL KATEGORI -->
<div x-show="modal==='cat'" @keydown.escape.window="modal=null" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
    <form method="POST" action="{{ route('categories.store') }}" class="bg-white rounded-lg p-5 w-full max-w-sm space-y-3">
        @csrf
        <h3 class="font-semibold">Tambah Kategori</h3>
        <input name="name" required placeholder="Nama kategori" class="w-full border rounded px-3 py-2 text-sm">
        <select name="parent_id" class="w-full border rounded px-3 py-2 text-sm">
            <option value="">— Kategori Utama —</option>
            <template x-for="m in mains" :key="m.id">
                <option :value="m.id" x-text="'Sub dari: ' + m.name"></option>
            </template>
        </select>
        <div class="flex justify-end gap-2">
            <button type="button" @click="modal=null" class="px-3 py-2 text-sm rounded bg-slate-200">Batal</button>
            <button class="px-3 py-2 text-sm rounded bg-blue-600 text-white">Simpan</button>
        </div>
    </form>
</div>

<!-- MODAL CATATAN -->
<div x-show="modal==='note'" @keydown.escape.window="modal=null" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
    <form method="POST" action="{{ route('notes.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg p-5 w-full max-w-lg space-y-3">
        @csrf
        <h3 class="font-semibold">Tambah Catatan / Dokumen</h3>
        <input name="title" required placeholder="Judul" class="w-full border rounded px-3 py-2 text-sm">
        <select name="category_id" required class="w-full border rounded px-3 py-2 text-sm">
            <option value="">— Pilih kategori —</option>
            <template x-for="c in flat" :key="c.id">
                <option :value="c.id" x-text="catPath(c.id)"></option>
            </template>
        </select>
        <div class="flex gap-4 text-sm">
            <label><input type="radio" name="type" value="text" x-model="ntype"> Teks / SQL</label>
            <label><input type="radio" name="type" value="pdf" x-model="ntype"> File PDF</label>
        </div>
        <textarea x-show="ntype==='text'" name="content" rows="8" placeholder="Isi panduan / kueri SQL" class="w-full border rounded px-3 py-2 text-sm font-mono"></textarea>
        <input x-show="ntype==='pdf'" type="file" name="file" accept=".pdf" class="w-full text-sm">
        <p x-show="ntype==='pdf'" class="text-xs text-slate-500">Maksimal 4 MB.</p>
        <div class="flex justify-end gap-2">
            <button type="button" @click="modal=null" class="px-3 py-2 text-sm rounded bg-slate-200">Batal</button>
            <button class="px-3 py-2 text-sm rounded bg-green-600 text-white">Simpan</button>
        </div>
    </form>
</div>

<script>
function kb() {
    return {
        tab: 'text', cat: null, q: '', modal: null, viewer: null, copied: null, ntype: 'text',
        cats: @json($categories),
        notes: @json($notes),
        get mains() { return this.cats.filter(c => !c.parent_id); },
        get flat() { return this.mains.flatMap(m => [m, ...this.childrenOf(m.id)]); },
        childrenOf(id) { return this.cats.filter(c => c.parent_id == id); },
        catPath(id) {
            const c = this.cats.find(x => x.id == id);
            if (!c) return '';
            const p = c.parent_id ? this.cats.find(x => x.id == c.parent_id) : null;
            return p ? p.name + ' › ' + c.name : c.name;
        },
        inCat(n) {
            if (this.cat === null) return true;
            if (n.category_id == this.cat) return true;
            const c = this.cats.find(x => x.id == this.cat);
            return !!c && !c.parent_id && this.cats.some(x => x.id == n.category_id && x.parent_id == c.id);
        },
        list(type) {
            const s = this.q.toLowerCase();
            return this.notes.filter(n =>
                n.type === type && this.inCat(n) &&
                (!s || ((n.title || '') + ' ' + (n.content || '') + ' ' + (n.file_name || '')).toLowerCase().includes(s))
            );
        },
        async copy(n) {
            try { await navigator.clipboard.writeText(n.content); }
            catch (e) {
                const t = document.createElement('textarea');
                t.value = n.content; document.body.appendChild(t); t.select();
                document.execCommand('copy'); t.remove();
            }
            this.copied = n.id;
            setTimeout(() => this.copied = null, 1500);
        }
    };
}
</script>
</body>
</html>
