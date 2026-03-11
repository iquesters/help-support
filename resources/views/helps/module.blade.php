@extends('help-support::layouts.app')

@section('content')

<div class="bg-body border-bottom py-4">
    <div class="container d-flex align-items-center gap-3">
        <a href="/help-support/helps.index" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        <h5 class="fw-bold mb-0 text-secondary"><i class="fa-solid fa-cubes me-2"></i>Module Documentation</h5>
    </div>
</div>

<div class="container py-3">
    <div class="row g-3" id="modulesContainer">
        <div class="col-12 text-center py-4 text-secondary">
            <i class="fa-solid fa-spinner fa-spin me-1"></i> Loading modules...
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const SANCTUM_TOKEN = '73|kj4hcKK3sDU5qycqLNeEdKooy32JJPYl7pXnMyjb9c037db4';

async function loadModules() {
    const container = document.getElementById('modulesContainer');
    try {
        const res = await fetch('https://messenger.iquesters.com/api/entity/list/modules', {
            headers: {
                'Accept':        'application/json',
                'Authorization': 'Bearer ' + SANCTUM_TOKEN
            }
        });
        if (!res.ok) {
            container.innerHTML = '<div class="col-12 text-center text-danger small py-4"><i class="fa-solid fa-triangle-exclamation me-1"></i>Token expired! Please update the token.</div>';
            return;
        }
        const data    = await res.json();
        const modules = data.response_schema.data;
        container.innerHTML = '';
        modules.forEach(function(mod) {
            const iconMeta = (mod.meta || []).find(m => m.meta_key === 'module_icon');
            const icon     = iconMeta ? iconMeta.meta_value : 'fa-solid fa-cube';
            container.innerHTML += `
    <div class="col-md-4 col-sm-6">
        <div class="border rounded-3 p-3 h-100">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="${icon} text-primary"></i>
                <h6 class="fw-bold small mb-0">${mod.name}</h6>
            </div>
            <p class="text-secondary small mb-2">${mod.description}</p>
            <a href="/help-support/helps.docs?module=${mod.name}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-book me-1"></i>View Docs
            </a>
        </div>
    </div>
`;
        });
    } catch(err) {
        container.innerHTML = '<div class="col-12 text-center text-danger small py-4"><i class="fa-solid fa-triangle-exclamation me-1"></i>Failed to load modules.</div>';
    }
}

document.addEventListener('DOMContentLoaded', loadModules);
</script>
@endpush