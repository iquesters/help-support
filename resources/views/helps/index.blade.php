@extends('helpsupport::layouts.app')

@section('content')
<div class="container-fluid">

    <h5 class="fs-6 text-muted mb-2">Help Articles</h5>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-muted text-uppercase">Title</th>
                            <th class="small text-muted text-uppercase">Status</th>
                            <th class="small text-muted text-uppercase text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No help articles available yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
