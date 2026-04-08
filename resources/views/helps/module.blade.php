@extends(app('app.layout'))

@section('content')

<div class="bg-body py-3">
    <div class="container-fluid px-2 d-flex align-items-center gap-3">
        <a href="{{ route('helpsupport.ui.show', ['viewName' => 'helps.index']) }}" class="btn btn-sm btn-outline-dark align-self-start">
            <i class="fa-solid fa-arrow-left me-1"></i>Back
        </a>
        <div>
            <h6 class="fw-bold mb-0 text-body"><i class="fa-solid fa-cubes me-2"></i>Module Documentation</h6>
            <p class="text-secondary small mb-0">Select a module to view its documentation</p>
        </div>
    </div>
</div>

<div class="container-fluid px-2 py-3">
    <div class="row g-3" id="modulesContainer">
        @forelse(($installedModules ?? collect()) as $mod)
            <div class="col-lg-4 col-md-6">
                <div class="border rounded-3 p-3 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="{{ $mod->getMeta('module_icon') ?: 'fa-solid fa-cube' }} text-primary"></i>
                        <h6 class="fw-bold small mb-0 text-body">{{ $mod->name }}</h6>
                    </div>
                    <p class="text-secondary small mb-3 flex-grow-1">{{ $mod->description }}</p>
                    <div>
                        <a href="{{ route('helpsupport.ui.show', ['viewName' => 'helps.docs']) }}?module={{ urlencode($mod->name) }}&uid={{ urlencode($mod->uid) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-book me-1"></i>View Docs
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-secondary">
                <i class="fa-solid fa-folder-open me-1"></i> No accessible modules found.
            </div>
        @endforelse
    </div>
</div>

@endsection
