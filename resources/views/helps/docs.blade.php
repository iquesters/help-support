@extends(app('app.layout'))

@section('content')

<div class="bg-body py-2">
    <div class="container-fluid px-2 d-flex align-items-center gap-3">
        <a href="{{ route('helpsupport.ui.show', ['viewName' => 'helps.module']) }}" class="btn btn-sm btn-outline-dark align-self-start">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        <div>
            <h6 class="fw-bold mb-0 text-body"><i class="fa-solid fa-book me-2"></i><span id="pageTitle">Documentation</span></h6>
            <small class="text-secondary" style="font-size:11px;">Browse and read module documentation</small>
        </div>
    </div>
</div>

<div class="container-fluid px-2 py-2">
    <div class="row g-2" style="min-height:85vh;">

        {{-- LEFT SIDEBAR --}}
        <div class="col-md-3">
            <div class="border rounded-3 p-2 h-100">
                <div id="fileList"></div>
            </div>
        </div>

        {{-- RIGHT CONTENT --}}
        <div class="col-md-9" style="position:sticky;top:1rem;height:fit-content;">
            <div class="border rounded-3 p-3" id="docContent" style="height:85vh;overflow-y:auto;"></div>
        </div>

    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>
<script>

function loaderHtml() {
    return `<div class="text-secondary small text-center py-5">
                <i class="fa-solid fa-spinner fa-spin fa-lg mb-2 d-block"></i>Loading...
            </div>`;
}
const params     = new URLSearchParams(window.location.search);
const moduleName = params.get('module') || '';
const moduleUid  = params.get('uid')    || '';
const repoName   = moduleName;
const filesApiBaseUrl = @json(route('helpsupport.docs.files', ['module' => '__MODULE__']));
const fileContentUrl = @json(route('helpsupport.docs.file'));

document.getElementById('pageTitle').textContent = moduleName + ' — Documents';

const renderer = new marked.Renderer();
renderer.heading = function(text, level) {
    const classes = {
        1: 'fs-6 fw-bold text-body mt-3 mb-1',
        2: 'fs-6 fw-semibold text-body mt-3 mb-1',
        3: 'small fw-semibold text-body mt-2 mb-1',
        4: 'small fw-semibold text-secondary mt-2 mb-1',
        5: 'small fw-semibold text-secondary mt-2 mb-1',
        6: 'small fw-semibold text-secondary mt-2 mb-1',
    };
    return `<p class="${classes[level] || 'small fw-bold'}">${text}</p>`;
};

// new additions

function buildTree(files) {
    const tree = {};
    files.forEach(file => {
        const parts = (file.path || file.name).replace(/^docs\//, '').replace('.md', '').split('/');
        let node = tree;
        parts.forEach((part, i) => {
            if (i === parts.length - 1) {
                if (!node.__files) node.__files = [];
                node.__files.push({ label: part.replace(/-/g, ' '), file });
            } else {
                if (!node[part]) node[part] = {};
                node = node[part];
            }
        });
    });
    return tree;
}

let firstBtn = null;

function renderTree(node, container, depth = 0) {
    (node.__files || []).forEach(({ label, file }) => {
        const btn = document.createElement('button');
        btn.className = 'btn btn-sm text-start mb-1 btn-outline-secondary';
        btn.style.marginLeft = `${depth * 16}px`;
        btn.style.width = `calc(100% - ${depth * 16}px)`;
        btn.innerHTML = `<i class="fa-regular fa-file-lines me-2"></i>${label}`;
        btn.onclick = () => loadFileContent(file.download_url, btn);
        container.appendChild(btn);
        if (!firstBtn) firstBtn = btn;
    });

    Object.keys(node).forEach(key => {
        if (key === '__files') return;

        const childWrap = document.createElement('div');
        childWrap.style.display = 'none';

        const folderBtn = document.createElement('button');
        folderBtn.className = 'btn btn-sm text-start mb-1 d-flex align-items-center gap-1 text-body border-0';
        folderBtn.style.marginLeft = `${depth * 16}px`;
        folderBtn.style.width = `calc(100% - ${depth * 16}px)`;
        folderBtn.innerHTML = `
            <i class="fa-solid fa-chevron-right fa-xs me-1 toggle-icon" style="transition:transform .2s;"></i>
            <i class="fa-regular fa-folder me-1 folder-icon"></i>
            <span class="small fw-medium">${key}</span>`;

        folderBtn.onclick = () => {
            const open = childWrap.style.display !== 'none';
            childWrap.style.display = open ? 'none' : 'block';
            folderBtn.querySelector('.toggle-icon').style.transform = open ? '' : 'rotate(90deg)';
            folderBtn.querySelector('.folder-icon').className = open
                ? 'fa-regular fa-folder me-1 folder-icon'
                : 'fa-regular fa-folder-open me-1 folder-icon';
        };

        container.appendChild(folderBtn);
        container.appendChild(childWrap);
        renderTree(node[key], childWrap, depth + 1);

        if (depth === 0 && Object.keys(node).indexOf(key) === 0) folderBtn.click();
    });
}

async function loadFileList() {
    const fileList   = document.getElementById('fileList');
    const docContent = document.getElementById('docContent');
    const apiUrl = filesApiBaseUrl.replace('__MODULE__', encodeURIComponent(repoName));

    fileList.innerHTML   = loaderHtml();
    docContent.innerHTML = loaderHtml();

    try {
        const res = await fetch(apiUrl);

        if (!res.ok) {
            fileList.innerHTML = `
                <div class="text-center text-secondary small py-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning d-block fa-lg mb-2"></i>
                    No documentation available.
                </div>`;
            docContent.innerHTML = `
                <div class="text-center text-secondary small py-5">
                    <i class="fa-solid fa-folder-open fa-lg mb-2 d-block"></i>
                    This module has no documents to display yet.
                    <br><small class="text-secondary">Check back later or contact your administrator.</small>
                </div>`;
            return;
        }

        const files   = await res.json();
        const mdFiles = files.filter(f => f.name.endsWith('.md'));

        if (!mdFiles.length) {
            fileList.innerHTML = `
                <div class="text-center text-secondary small py-3">
                    <i class="fa-solid fa-triangle-exclamation text-warning d-block fa-lg mb-2"></i>
                    No documentation available.
                </div>`;
            docContent.innerHTML = `
                <div class="text-center text-secondary small py-5">
                    <i class="fa-solid fa-folder-open fa-lg mb-2 d-block"></i>
                    This module has no documents to display yet.
                    <br><small class="text-secondary">Check back later or contact your administrator.</small>
                </div>`;
            return;
        }

        fileList.innerHTML = '';
        firstBtn = null;
        renderTree(buildTree(mdFiles), fileList);
        if (firstBtn) firstBtn.click();

    } catch(err) {
        fileList.innerHTML = `
            <div class="text-center text-danger small py-3">
                <i class="fa-solid fa-circle-xmark d-block fa-lg mb-2"></i>
                Failed to load documents.
            </div>`;
        docContent.innerHTML = `
            <div class="text-center text-danger small py-5">
                <i class="fa-solid fa-circle-xmark fa-lg mb-2 d-block"></i>
                Failed to load documents.
            </div>`;
    }
}

async function loadFileContent(url, activeBtn) {
    const docContent = document.getElementById('docContent');

    docContent.innerHTML = loaderHtml();

    document.querySelectorAll('#fileList button').forEach(b => {
        b.classList.remove('btn-dark', 'text-white');
        b.classList.add('btn-outline-secondary');
    });
    activeBtn.classList.remove('btn-outline-secondary');
    activeBtn.classList.add('btn-dark', 'text-white');

    try {
        const res  = await fetch(`${fileContentUrl}?url=${encodeURIComponent(url)}`);
        const text = await res.text();
        docContent.innerHTML = `<div class="markdown-content small">${marked.parse(text, { renderer })}</div>`;
    } catch(err) {
        docContent.innerHTML = `<div class="text-center text-danger small py-5"><i class="fa-solid fa-circle-xmark d-block fa-lg mb-2"></i>Failed to load document.</div>`;
    }
}

document.addEventListener('DOMContentLoaded', loadFileList);
</script>
@endpush
