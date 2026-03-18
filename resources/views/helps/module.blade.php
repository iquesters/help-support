@extends('help-support::layouts.app')

@section('content')

<div class="bg-body border-bottom py-3">
    <div class="container-fluid px-4 d-flex align-items-center gap-3">
        <a href="/help-support/helps.index" class="btn btn-sm btn-outline-primary align-self-start">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        <div>
            <h5 class="fw-bold mb-0 text-body"><i class="fa-solid fa-cubes me-2 text-primary"></i>Module Documentation</h5>
            <small class="text-secondary">Select a module to view its documentation</small>
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-3">
    <div class="row g-3" id="modulesContainer">
        <div class="col-12 text-center py-4 text-secondary">
            <i class="fa-solid fa-spinner fa-spin me-1"></i> Loading modules...
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const SANCTUM_TOKEN = '79|0r1tKP0WcXAHr3dPVxj7Ahxp4zgzrOd1D4q0ixnKfdde7f15';

async function loadModules() {
    const container = document.getElementById('modulesContainer');
    try {
        const res = await fetch('https://vps.knorai.com/api/entity/list/modules', {
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
                <div class="col-lg-4 col-md-6">
                    <div class="border rounded-3 p-3 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-2 bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center">
                                <i class="${icon} text-primary"></i>
                            </div>
                            <h6 class="fw-bold small mb-0 text-body">${mod.name}</h6>
                        </div>
                        <p class="text-secondary small mb-3 flex-grow-1">${mod.description}</p>
                        <div>
    <a href="/help-support/helps.docs?module=${mod.name}&uid=${mod.uid}" class="btn btn-sm btn-outline-primary">
        <i class="fa-solid fa-book me-1"></i>View Docs
    </a>
</div>
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