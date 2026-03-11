@extends('help-support::layouts.app')

@section('content')

<div class="bg-body border-bottom py-3">
    <div class="container d-flex align-items-center gap-3">
        <a href="/help-support/helps.module" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        <h5 class="fw-bold mb-0 text-secondary"><i class="fa-solid fa-book me-2"></i><span id="pageTitle">Documentation</span></h5>
    </div>
</div>

<div class="container-fluid py-3">
    <div class="row g-3" style="min-height:80vh;">

        {{-- LEFT SIDEBAR --}}
        <div class="col-md-3">
            <div class="border rounded-3 p-3 h-100">
                <p class="fw-bold small text-secondary mb-3"><i class="fa-solid fa-folder-open me-2"></i>Documents</p>
                <div id="fileList">
                    <div class="text-secondary small text-center py-3">
                        <i class="fa-solid fa-spinner fa-spin me-1"></i>Loading...
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT CONTENT --}}
        <div class="col-md-9">
            <div class="border rounded-3 p-4 h-100" id="docContent">
                <div class="text-secondary small text-center py-5">
                    <i class="fa-solid fa-hand-point-left fa-lg mb-2 d-block"></i>
                    Select a document from the left to view it
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>
<script>
const repoMap = {
    'user-interface':  'user-interface',
    'user-management': 'user-management',
    'organisation':    'organisation',
    'smart-messenger': 'smart-messenger',
    'integration':     'integration',
    'foundation':      'foundation',
    'dev':             'dev',
};

const params     = new URLSearchParams(window.location.search);
const moduleName = params.get('module') || '';
const repoName   = repoMap[moduleName] || moduleName;

document.getElementById('pageTitle').textContent = moduleName + ' — Docs';

async function loadFileList() {
    const fileList = document.getElementById('fileList');
    const apiUrl   = `https://api.github.com/repos/iquesters/${repoName}/contents/docs`;

    try {
        const res  = await fetch(apiUrl, { headers: { 'Accept': 'application/vnd.github.v3+json' } });

        if (!res.ok) {
            fileList.innerHTML = `
                <div class="text-center text-secondary small py-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning d-block fa-lg mb-2"></i>
                    No documentation available for this module.
                </div>`;
            return;
        }

        const files = await res.json();
        const mdFiles = files.filter(f => f.name.endsWith('.md'));

        if (!mdFiles.length) {
            fileList.innerHTML = `
                <div class="text-center text-secondary small py-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning d-block fa-lg mb-2"></i>
                    No documentation available for this module.
                </div>`;
            return;
        }

        fileList.innerHTML = '';
        mdFiles.forEach(function(file, index) {
            const btn = document.createElement('button');
            btn.className   = 'btn btn-sm w-100 text-start mb-2 btn-outline-secondary';
            btn.innerHTML   = `<i class="fa-regular fa-file-lines me-2"></i>${file.name.replace('.md', '').replace(/-/g, ' ')}`;
            btn.onclick     = () => loadFileContent(file.download_url, btn);
            fileList.appendChild(btn);
            if (index === 0) btn.click();
        });

    } catch(err) {
        fileList.innerHTML = `
            <div class="text-center text-danger small py-3">
                <i class="fa-solid fa-circle-xmark d-block fa-lg mb-2"></i>
                Failed to load documents.
            </div>`;
    }
}

async function loadFileContent(url, activeBtn) {
    const docContent = document.getElementById('docContent');

    document.querySelectorAll('#fileList button').forEach(b => {
        b.classList.remove('btn-secondary', 'text-white');
        b.classList.add('btn-outline-secondary');
    });
    activeBtn.classList.remove('btn-outline-secondary');
    activeBtn.classList.add('btn-secondary', 'text-white');

    docContent.innerHTML = `<div class="text-secondary small text-center py-5"><i class="fa-solid fa-spinner fa-spin me-1"></i>Loading...</div>`;

    try {
        const res  = await fetch(url);
        const text = await res.text();
        docContent.innerHTML = `<div class="markdown-content">${marked.parse(text)}</div>`;
    } catch(err) {
        docContent.innerHTML = `<div class="text-center text-danger small py-5"><i class="fa-solid fa-circle-xmark d-block fa-lg mb-2"></i>Failed to load document.</div>`;
    }
}

document.addEventListener('DOMContentLoaded', loadFileList);
</script>
@endpush