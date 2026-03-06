@extends('help-support::layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body{background:#F0F2F5;font-family:'Inter',sans-serif;}
.hero-section{background:#fff;border-bottom:1px solid #E5E7EB;padding:56px 0 48px;}
.hero-section h2{font-size:2rem;font-weight:700;color:#111;}
.hero-section p{color:#6B7280;font-size:1rem;}
.search-wrapper{position:relative;}
.search-wrapper .search-icon{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:#9CA3AF;font-size:15px;z-index:2;}
.search-input{border-radius:50px;padding:14px 20px 14px 48px;font-size:15px;border:1.5px solid #E5E7EB;background:#F9FAFB;box-shadow:none;outline:none;width:100%;}
.search-input:focus{border-color:#00BCD4;background:#fff;box-shadow:0 0 0 3px rgba(0,188,212,0.1);}
.panel-card{background:#fff;border-radius:16px;border:1px solid #E5E7EB;box-shadow:0 2px 12px rgba(0,0,0,0.04);padding:28px;height:100%;}
.section-title{font-size:1rem;font-weight:700;color:#111;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.module-pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:22px;}
.module-pill{background:#fff;border:1.5px solid #D1D5DB;color:#374151;padding:5px 18px;border-radius:50px;font-size:13px;font-weight:500;text-decoration:none;transition:all .2s;display:inline-block;}
.module-pill:hover{border-color:#00BCD4;color:#00BCD4;text-decoration:none;}
.module-pill.active{background:#00BCD4;border-color:#00BCD4;color:#fff;text-decoration:none;}
.doc-card{background:#fff;border-radius:12px;padding:20px;border:1px solid #E5E7EB;transition:all .2s ease;cursor:pointer;height:100%;text-decoration:none;display:block;color:inherit;}
.doc-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.07);border-color:#00BCD4;text-decoration:none;color:inherit;}
.doc-card-icon{width:42px;height:42px;border-radius:10px;background:#F0FAFB;display:flex;align-items:center;justify-content:center;margin-bottom:14px;font-size:19px;}
.doc-card h6{font-size:14px;font-weight:600;color:#111;margin-bottom:6px;}
.doc-card p{font-size:12.5px;color:#6B7280;margin-bottom:12px;line-height:1.55;}
.doc-card .updated{font-size:11.5px;color:#9CA3AF;display:flex;align-items:center;gap:4px;}
.ai-header{background:#0F172A;border-radius:12px;padding:18px 20px;color:white;margin-bottom:20px;display:flex;align-items:center;gap:14px;}
.ai-avatar{width:40px;height:40px;background:#00BCD4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.ai-header h5{font-size:14px;font-weight:600;margin:0 0 2px;}
.ai-header small{opacity:.6;font-size:12px;}
.chat-messages{min-height:180px;max-height:240px;overflow-y:auto;margin-bottom:16px;display:flex;flex-direction:column;gap:10px;padding-right:4px;}
.chat-messages::-webkit-scrollbar{width:4px;}
.chat-messages::-webkit-scrollbar-thumb{background:#E5E7EB;border-radius:4px;}
.chat-bubble{padding:11px 15px;border-radius:12px;font-size:13px;line-height:1.55;max-width:90%;}
.chat-bubble.bot{background:#F3F4F6;color:#374151;border-bottom-left-radius:4px;align-self:flex-start;}
.chat-bubble.user{background:#0F172A;color:#fff;border-bottom-right-radius:4px;align-self:flex-end;}
.chat-input-row{display:flex;gap:8px;align-items:center;}
.chat-input-row input{border-radius:50px;border:1.5px solid #E5E7EB;padding:10px 18px;font-size:13px;flex:1;outline:none;font-family:'Inter',sans-serif;}
.chat-input-row input:focus{border-color:#00BCD4;}
.chat-send-btn{width:38px;height:38px;border-radius:50%;background:#00BCD4;border:none;color:white;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;}
.chat-send-btn:hover{background:#0097A7;}
.faq-section{margin-top:28px;}
.accordion-button{font-size:14px;font-weight:500;border-radius:10px !important;font-family:'Inter',sans-serif;}
.accordion-button:not(.collapsed){background:#F0FAFB;color:#00BCD4;box-shadow:none;}
.accordion-button:focus{box-shadow:none;}
.accordion-item{border:1px solid #E5E7EB !important;border-radius:10px !important;margin-bottom:8px;overflow:hidden;}
.accordion-body{font-size:13px;color:#6B7280;line-height:1.65;padding-top:4px;}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="hero-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2>How can we help you?</h2>
            <p>Search our documentation or ask the AI assistant below</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="docSearch" class="search-input" placeholder="Search documentation..." autocomplete="off">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DOCS + AI --}}
<div class="container py-5">
    <div class="row g-4">

        {{-- LEFT: DOCS --}}
        <div class="col-lg-7">
            <div class="panel-card">
                <div class="section-title">
                    <span>📋</span> Module Documentation
                </div>
                <div class="module-pills">
                    <a href="/help-support/docs.user_users" class="module-pill active">Users</a>
                    <a href="/help-support/docs.user_roles" class="module-pill">Roles</a>
                    <a href="/help-support/docs.user_perms" class="module-pill">Permissions</a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_users" class="doc-card">
                            <div class="doc-card-icon">📄</div>
                            <h6>Getting Started</h6>
                            <p>Learn the basics of the platform and set up your workspace.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 2 days ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_users" class="doc-card">
                            <div class="doc-card-icon">👤</div>
                            <h6>User Management</h6>
                            <p>Complete guide to creating, editing, and managing users.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 1 week ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_roles" class="doc-card">
                            <div class="doc-card-icon">🛡️</div>
                            <h6>Roles & Access</h6>
                            <p>Understand role-based access control and how to assign roles.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 3 days ago</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/help-support/docs.user_perms" class="doc-card">
                            <div class="doc-card-icon">🔑</div>
                            <h6>Permissions Overview</h6>
                            <p>Deep dive into granular permission management across modules.</p>
                            <span class="updated"><i class="fa-regular fa-clock"></i> Updated 5 days ago</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: AI --}}
        <div class="col-lg-5">
            <div class="panel-card">
                <div class="ai-header">
                    <div class="ai-avatar">🤖</div>
                    <div>
                        <h5>AI Assistant</h5>
                        <small>Ask me anything about the docs</small>
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="chat-bubble bot">👋 Hello! I can help you find anything in the documentation. What would you like to know?</div>
                </div>
                <div class="chat-input-row">
                    <input type="text" id="chatInput" placeholder="Type your question..." onkeydown="handleChatKey(event)">
                    <button class="chat-send-btn" onclick="sendChat()">
                        <i class="fa-solid fa-paper-plane" style="font-size:13px"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- FAQ --}}
<div class="container pb-5 faq-section">
    <div class="panel-card">
        <div class="section-title">
            <span>💬</span> Frequently Asked Questions
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