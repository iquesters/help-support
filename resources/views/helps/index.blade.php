@extends('help-support::layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body{background:var(--bs-light);font-family:'Inter',sans-serif;}

/* HERO */
.hero-section{background:var(--bs-white);border-bottom:1px solid var(--bs-border-color);padding:36px 0 28px;}
.hero-section h2{font-size:1.9rem;font-weight:700;color:var(--bs-dark);margin-bottom:6px;}
.hero-section p{color:var(--bs-secondary);font-size:.95rem;margin-bottom:0;}

/* SEARCH */
.search-wrapper{position:relative;}
.search-wrapper .search-icon{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:var(--bs-secondary);font-size:14px;z-index:2;}
.search-input{border-radius:50px;padding:13px 22px 13px 46px;font-size:14px;border:1.5px solid var(--bs-border-color);background:var(--bs-light);width:100%;outline:none;}
.search-input:focus{border-color:var(--bs-info);background:var(--bs-white);box-shadow:0 0 0 3px rgba(13,202,240,0.15);}

/* PANELS */
.panel-card{background:var(--bs-white);border-radius:14px;border:1px solid var(--bs-border-color);box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:22px;height:100%;display:flex;flex-direction:column;}

/* SECTION TITLE */
.section-title{font-size:.95rem;font-weight:700;color:var(--bs-dark);margin-bottom:16px;display:flex;align-items:center;gap:8px;}

/* MODULE PILLS */
.module-pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;}
.module-pill{background:var(--bs-white);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);padding:5px 16px;border-radius:50px;font-size:13px;font-weight:500;text-decoration:none;transition:.2s;}
.module-pill:hover{border-color:var(--bs-info);color:var(--bs-info);text-decoration:none;}
.module-pill.active{background:var(--bs-info);border-color:var(--bs-info);color:var(--bs-white);text-decoration:none;}

/* DOC CARDS */
.doc-card{background:var(--bs-white);border-radius:12px;padding:18px;border:1px solid var(--bs-border-color);transition:.2s ease;height:100%;text-decoration:none;display:block;color:inherit;}
.doc-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.07);border-color:var(--bs-info);text-decoration:none;color:inherit;}
.doc-card-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:16px;}
.doc-card h6{font-size:13px;font-weight:600;color:var(--bs-dark);margin-bottom:4px;}
.doc-card p{font-size:12.5px;color:var(--bs-secondary);margin-bottom:10px;line-height:1.55;}
.doc-card .updated{font-size:11px;color:var(--bs-secondary);display:flex;align-items:center;gap:4px;}

/* AI */
.ai-header{background:var(--bs-dark);border-radius:12px;padding:16px 18px;color:var(--bs-white);margin-bottom:16px;display:flex;align-items:center;gap:12px;}
.ai-avatar{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--bs-white);flex-shrink:0;}
.ai-header h5{font-size:14px;font-weight:600;margin:0 0 2px;}
.ai-header small{opacity:.6;font-size:12px;}
.chat-messages{flex:1;overflow-y:auto;margin-bottom:12px;display:flex;flex-direction:column;gap:8px;min-height:160px;max-height:200px;}
.chat-messages::-webkit-scrollbar{width:3px;}
.chat-messages::-webkit-scrollbar-thumb{background:var(--bs-border-color);border-radius:4px;}
.chat-bubble{padding:10px 14px;border-radius:12px;font-size:13px;line-height:1.55;max-width:90%;}
.chat-bubble.bot{background:var(--bs-light);color:var(--bs-body-color);border-bottom-left-radius:4px;align-self:flex-start;}
.chat-bubble.user{background:var(--bs-dark);color:var(--bs-white);border-bottom-right-radius:4px;align-self:flex-end;}
.chat-input-row{display:flex;gap:8px;align-items:center;margin-top:auto;}
.chat-input-row input{border-radius:50px;border:1.5px solid var(--bs-border-color);padding:9px 16px;font-size:13px;flex:1;outline:none;font-family:'Inter',sans-serif;}
.chat-input-row input:focus{border-color:var(--bs-info);}
.chat-send-btn{width:36px;height:36px;border-radius:50%;background:var(--bs-info);border:none;color:var(--bs-white);display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;}
.chat-send-btn:hover{filter:brightness(.9);}

/* FAQ */
.faq-section{margin-top:16px;}
.accordion-button{font-size:13.5px;font-weight:500;border-radius:10px !important;font-family:'Inter',sans-serif;}
.accordion-button:not(.collapsed){background:var(--bs-info-bg-subtle);color:var(--bs-info);box-shadow:none;}
.accordion-button:focus{box-shadow:none;}
.accordion-item{border:1px solid var(--bs-border-color) !important;border-radius:10px !important;margin-bottom:8px;overflow:hidden;}
.accordion-body{font-size:13px;color:var(--bs-secondary);line-height:1.65;padding-top:4px;}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero-section">
    <div class="container-fluid px-4">
        <div class="text-center mb-3">
            <h2>How can we help you?</h2>
            <p class="mt-1">Search our documentation or ask the AI assistant below</p>
        </div>
        <div class="row justify-content-center mt-3">
            <div class="col-md-6 col-lg-4">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="docSearch" class="search-input" placeholder="Search documentation..." autocomplete="off">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DOCS + AI --}}
<div class="container-fluid px-4 py-3">
    <div class="row g-3">

        {{-- LEFT: DOCS --}}
        <div class="col-lg-8">
            <div class="panel-card">
                <div class="section-title">
                    <i class="fa-solid fa-book-open text-primary"></i> Module Documentation
                </div>
                <div class="module-pills">
                    <a href="/help-support/docs.user_users" class="module-pill">Users</a>
                    <a href="/help-support/docs.user_roles" class="module-pill">Roles</a>
                    <a href="/help-support/docs.user_perms" class="module-pill">Permissions</a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_users" class="doc-card">
                            <div class="doc-card-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <h6>Getting Started</h6>
                            <p>Learn the basics of the platform and set up your workspace.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 2 days ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_users" class="doc-card">
                            <div class="doc-card-icon bg-info bg-opacity-10 text-info">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <h6>User Management</h6>
                            <p>Complete guide to creating, editing, and managing users.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 1 week ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_roles" class="doc-card">
                            <div class="doc-card-icon bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h6>Roles & Access</h6>
                            <p>Understand role-based access control and how to assign roles.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 3 days ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_perms" class="doc-card">
                            <div class="doc-card-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <h6>Permissions Overview</h6>
                            <p>Deep dive into granular permission management across modules.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 5 days ago</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: AI --}}
        <div class="col-lg-4">
            <div class="panel-card">
                <div class="ai-header">
                    <div class="ai-avatar bg-info">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">AI Assistant</h5>
                        <small>Ask me anything about the docs</small>
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="chat-bubble bot">👋 Hello! I can help you find anything in the documentation. What would you like to know?</div>
                </div>
                <div class="chat-input-row">
                    <input type="text" id="chatInput" placeholder="Type your question..." onkeydown="handleChatKey(event)">
                    <button class="chat-send-btn" onclick="sendChat()">
                        <i class="fa-solid fa-paper-plane" style="font-size:12px"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- FAQ --}}
<div class="container-fluid px-4 pb-4 faq-section">
    <div class="panel-card">
        <div class="section-title">
            <i class="fa-solid fa-circle-question text-primary"></i> Frequently Asked Questions
        </div>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq1">
                        How do I get started?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Start by reviewing the Users documentation and creating your first user account.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How do roles and permissions work together?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Roles are collections of permissions. Assign a role to a user and they inherit all permissions automatically.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Is there a rate limit for API requests?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Rate limits depend on your subscription plan. Refer to the API documentation for your tier's limits.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                        How accurate is the AI documentation assistant?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">The assistant uses documentation context to generate responses and improves as more docs are added.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('docSearch').addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    const val = this.value.toLowerCase().trim();
    if (!val) return;
    if      (val.includes('user')) window.location.href = '/help-support/docs.user_users';
    else if (val.includes('role')) window.location.href = '/help-support/docs.user_roles';
    else if (val.includes('perm')) window.location.href = '/help-support/docs.user_perms';
});
function handleChatKey(e) { if (e.key === 'Enter') sendChat(); }
function sendChat() {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;
    appendBubble(text, 'user');
    input.value = '';
    setTimeout(() => appendBubble("I'm looking that up for you... (AI integration coming soon)", 'bot'), 600);
}
function appendBubble(text, role) {
    const box = document.getElementById('chatMessages');
    const b = document.createElement('div');
    b.className = 'chat-bubble ' + role;
    b.textContent = text;
    box.appendChild(b);
    box.scrollTop = box.scrollHeight;
}
</script>
@endpush