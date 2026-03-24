@extends(app('app.layout'))

    @section('content')

    <div class="bg-body border-bottom py-3">
        <div class="container-fluid px-4">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-circle-question me-2"></i>How can we help you?</h6>
            <p class="text-secondary small mb-3">Search our documentation or ask the AI assistant below</p>
            <div class="col-md-5 col-lg-4 px-0">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
                    <input type="text" id="docSearch" class="form-control" placeholder="Search documentation..." autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 py-3">
        <div class="row g-3 align-items-stretch">

            <div class="col-lg-7">
                <div class="border rounded-3 p-3 h-100">
                    <p class="fw-bold small mb-3"><i class="fa-solid fa-book me-2"></i>Module Documentation</p>
                    <div class="row g-2">
                        @foreach([
                            ['url'=>route('helpsupport.ui.show', ['viewName' => 'helps.module']),'title'=>'Getting Started', 'desc'=>'Learn the basics of Iquesters',   'updated'=>'2 days ago', 'link'=>true],
                            ['url'=>'#',                         'title'=>'User Guide',       'desc'=>'Complete guide for end users',    'updated'=>'1 week ago', 'link'=>false],
                            ['url'=>'#',                         'title'=>'Best Practices',   'desc'=>'Tips for optimal usage',          'updated'=>'3 days ago', 'link'=>false],
                            ['url'=>'#',                         'title'=>'Troubleshooting',  'desc'=>'Common issues and solutions',     'updated'=>'5 days ago', 'link'=>false],
                        ] as $card)
                        <div class="col-md-6">
                            @if($card['link'])
                                <a href="{{ $card['url'] }}" class="text-body text-decoration-none border rounded-3 p-3 h-100 d-block">
                            @else
                                <div class="border rounded-3 p-3 h-100">
                            @endif
                                <i class="fa-regular fa-file-lines d-block mb-2" style="font-size:1.2rem;"></i>
                                <h6 class="fw-bold small mb-1">{{ $card['title'] }}</h6>
                                <p class="text-secondary small mb-2">{{ $card['desc'] }}</p>
                                <span class="text-secondary" style="font-size:11px;"><i class="fa-regular fa-clock me-1"></i>Updated {{ $card['updated'] }}</span>
                            @if($card['link'])
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="border rounded-3 h-100 d-flex flex-column p-3">
                    <div class="rounded-3 p-3 mb-3 bg-body-tertiary text-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                            <i class="fa-solid fa-robot small text-white"></i>
                        </div>
                        <div>
                            <p class="fw-semibold small mb-0">AI Assistant</p>
                            <small class="text-secondary" style="font-size:11px;">Ask me anything about the docs</small>
                        </div>
                        <span class="badge bg-success ms-auto" style="font-size:10px;"><i class="fa-solid fa-circle me-1" style="font-size:7px;"></i>Online</span>
                    </div>
                    <div class="flex-grow-1 overflow-auto mb-3 d-flex flex-column gap-2" id="chatMessages" style="min-height:0;max-height:280px;">
                        <div class="align-self-start bg-body-secondary text-body rounded-3 px-3 py-2 small">
                            <i class="fa-solid fa-hand-wave me-1"></i> Hello! I can help you find anything in the documentation. What would you like to know?
                        </div>
                    </div>
                    <div class="input-group mt-auto">
                        <input type="text" id="chatInput" class="form-control form-control-sm" placeholder="Type your question..." onkeydown="handleChatKey(event)">
                        <button class="btn btn-info btn-sm text-white" onclick="sendChat()"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid px-4 pb-4">
        <div class="border rounded-3 p-3">
            <p class="fw-bold small mb-3"><i class="fa-solid fa-circle-question me-2"></i>Frequently Asked Questions</p>
            <div class="accordion" id="faqAccordion">
                @foreach([
                    ['id'=>'faq1','q'=>'How do I get started?',                         'a'=>'Start by reviewing the Users documentation and creating your first user account.'],
                    ['id'=>'faq2','q'=>'How do roles and permissions work together?',    'a'=>'Roles are collections of permissions. Assign a role to a user and they inherit all permissions automatically.'],
                    ['id'=>'faq3','q'=>'Is there a rate limit for API requests?',        'a'=>'Rate limits depend on your subscription plan. Refer to the API documentation for your tier\'s limits.'],
                    ['id'=>'faq4','q'=>'How accurate is the AI documentation assistant?','a'=>'The assistant uses rule-based responses from the documentation context and improves as more rules are added.'],
                ] as $faq)
                <div class="accordion-item border rounded-3 mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed small fw-medium" data-bs-toggle="collapse" data-bs-target="#{{ $faq['id'] }}">
                            <i class="fa-solid fa-play fa-xs me-2 text-info"></i>{{ $faq['q'] }}
                        </button>
                    </h2>
                    <div id="{{ $faq['id'] }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body small text-secondary">{{ $faq['a'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function handleChatKey(e) { if (e.key === 'Enter') sendChat(); }
function sendChat() {
    const input = document.getElementById('chatInput');
    const text  = input.value.trim();
    if (!text) return;
    appendBubble(text, 'user');
    input.value = '';
    setTimeout(() => appendBubble('I cannot help you with this right now.', 'bot'), 400);
}
function appendBubble(text, role) {
    const box = document.getElementById('chatMessages');
    const b   = document.createElement('div');
    b.className = role === 'user' ? 'align-self-end bg-primary text-white rounded-3 px-3 py-2 small' : 'align-self-start bg-body-secondary text-body rounded-3 px-3 py-2 small';
    b.textContent = text;
    box.appendChild(b);
    box.scrollTop = box.scrollHeight;
}
</script>
@endpush
