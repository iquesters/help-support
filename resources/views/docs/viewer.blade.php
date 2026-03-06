@extends('help-support::layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body{background:#F0F2F5;font-family:'Inter',sans-serif;}
.topbar{background:#fff;border-bottom:1px solid #E5E7EB;padding:14px 0;}
.back-btn{display:inline-flex;align-items:center;gap:6px;color:#6B7280;font-size:13px;font-weight:500;text-decoration:none;transition:color .2s;}
.back-btn:hover{color:#00BCD4;text-decoration:none;}
.doc-title{font-size:1.1rem;font-weight:700;color:#111;}
.doc-badge{background:#F0FAFB;border:1px solid #E5E7EB;color:#00BCD4;font-size:11px;font-weight:600;padding:3px 10px;border-radius:50px;}
.panel-card{background:#fff;border-radius:16px;border:1px solid #E5E7EB;box-shadow:0 2px 12px rgba(0,0,0,0.04);padding:36px;height:100%;}
.markdown-body{font-size:14px;line-height:1.75;color:#374151;}
.markdown-body h1{font-size:1.6rem;font-weight:700;color:#111;margin:0 0 8px;}
.markdown-body h2{font-size:1.15rem;font-weight:700;color:#111;margin:28px 0 10px;padding-bottom:6px;border-bottom:1px solid #E5E7EB;}
.markdown-body h3{font-size:1rem;font-weight:600;color:#111;margin:20px 0 8px;}
.markdown-body p{margin:0 0 14px;}
.markdown-body ul,.markdown-body ol{padding-left:20px;margin:0 0 14px;}
.markdown-body li{margin-bottom:4px;}
.markdown-body code{background:#F3F4F6;padding:2px 6px;border-radius:4px;font-size:12.5px;font-family:monospace;color:#374151;}
.markdown-body pre{background:#0F172A;color:#e2e8f0;padding:16px 20px;border-radius:10px;overflow-x:auto;margin:0 0 16px;}
.markdown-body pre code{background:none;padding:0;color:inherit;font-size:13px;}
.markdown-body blockquote{border-left:3px solid #00BCD4;padding:8px 16px;margin:0 0 14px;background:#F0FAFB;border-radius:0 8px 8px 0;color:#6B7280;}
.markdown-body a{color:#00BCD4;text-decoration:none;}
.markdown-body a:hover{text-decoration:underline;}
.markdown-body table{width:100%;border-collapse:collapse;margin:0 0 16px;font-size:13px;}
.markdown-body th{background:#F9FAFB;padding:10px 14px;text-align:left;font-weight:600;border:1px solid #E5E7EB;color:#111;}
.markdown-body td{padding:10px 14px;border:1px solid #E5E7EB;color:#374151;}
.markdown-body tr:hover td{background:#F9FAFB;}
.markdown-body hr{border:none;border-top:1px solid #E5E7EB;margin:24px 0;}
.not-found{text-align:center;padding:60px 20px;color:#6B7280;}
.not-found .icon{font-size:48px;margin-bottom:16px;}
.not-found h3{font-size:1.1rem;font-weight:600;color:#111;margin-bottom:8px;}
.not-found p{font-size:13px;}
.toc-card{background:#fff;border-radius:16px;border:1px solid #E5E7EB;box-shadow:0 2px 12px rgba(0,0,0,0.04);padding:20px;position:sticky;top:20px;}
.toc-title{font-size:12px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;}
.toc-link{display:block;font-size:13px;color:#6B7280;text-decoration:none;padding:4px 0;border-left:2px solid transparent;padding-left:10px;transition:all .2s;}
.toc-link:hover{color:#00BCD4;border-left-color:#00BCD4;text-decoration:none;}
.toc-link.h3{padding-left:22px;font-size:12px;}
</style>
@endpush

@section('content')

{{-- TOPBAR --}}
<div class="topbar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="/help-support/helps.index" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Help & Support
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="doc-title">{{ $title }}</span>
            <span class="doc-badge">Documentation</span>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<div class="container py-4">
    <div class="row g-4">

        {{-- MAIN DOC --}}
        <div class="col-lg-9">
            <div class="panel-card">
                @if($content)
                    <div class="markdown-body">
                        {!! $content !!}
                    </div>
                @else
                    <div class="not-found">
                        <div class="icon">📄</div>
                        <h3>Documentation not available</h3>
                        <p>This document could not be loaded. Please try again later.</p>
                        <a href="/help-support/helps.index" class="back-btn mt-3">
                            <i class="fa-solid fa-arrow-left"></i> Back to Help & Support
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- SIDEBAR TOC --}}
        <div class="col-lg-3">
            <div class="toc-card">
                <div class="toc-title">On this page</div>
                <div id="tocLinks">
                    <span style="font-size:12px;color:#9CA3AF;">Loading...</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-generate TOC from headings in the markdown body
document.addEventListener('DOMContentLoaded', function() {
    const body    = document.querySelector('.markdown-body');
    const toc     = document.getElementById('tocLinks');
    if (!body || !toc) return;

    const headings = body.querySelectorAll('h2, h3');
    if (!headings.length) { toc.innerHTML = '<span style="font-size:12px;color:#9CA3AF;">No sections found</span>'; return; }

    toc.innerHTML = '';
    headings.forEach(function(h, i) {
        const id = 'section-' + i;
        h.id = id;
        const a = document.createElement('a');
        a.href = '#' + id;
        a.className = 'toc-link' + (h.tagName === 'H3' ? ' h3' : '');
        a.textContent = h.textContent;
        toc.appendChild(a);
    });
});
</script>
@endpush