@extends('layouts.app')

@section('title', 'Welcome — Quiz System')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-sm-8 col-md-6 col-lg-5">

        <div class="text-center mb-4">
            <div class="display-1 mb-2">🌍</div>
            <h1 class="h3 fw-bold">Geography &amp; Current Affairs Quiz</h1>
            <p class="text-muted">Test your knowledge with 5 random questions.</p>
        </div>

        <div class="card p-4">
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('user.store') }}" method="POST" id="start-form">
                @csrf
                <input type="hidden" name="resume_id" id="resume-id" value="">

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Your Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control form-control-lg"
                        placeholder="Enter your name…"
                        value="{{ old('name') }}"
                        autofocus
                        autocomplete="off"
                        required
                    >
                </div>

                {{-- Resume banner (hidden by default) --}}
                <div id="resume-banner" class="alert alert-info py-2 d-none mb-3" role="alert">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    <span id="resume-text"></span>
                    <div class="mt-2 d-flex gap-2">
                        <button type="button" id="btn-resume" class="btn btn-sm btn-info text-white">
                            Resume Quiz
                        </button>
                        <button type="button" id="btn-fresh" class="btn btn-sm btn-outline-secondary">
                            Start Fresh
                        </button>
                    </div>
                </div>

                <div class="d-grid" id="start-btn-wrap">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Start Quiz <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-muted mt-3" style="font-size:.85rem;">
            Questions are served in random order every time.
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
    const CHECK_URL    = '{{ route("user.checkResume") }}';

    const nameInput    = document.getElementById('name');
    const resumeBanner = document.getElementById('resume-banner');
    const resumeText   = document.getElementById('resume-text');
    const resumeIdEl   = document.getElementById('resume-id');
    const btnResume    = document.getElementById('btn-resume');
    const btnFresh     = document.getElementById('btn-fresh');

    let debounceTimer  = null;
    let pendingUserId  = null;

    function checkResume(name) {
        if (name.length < 2) {
            resumeBanner.classList.add('d-none');
            return;
        }

        fetch(CHECK_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ name }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.resumable) {
                pendingUserId = data.user_id;
                resumeText.textContent =
                    `Welcome back! You have ${data.remaining} question(s) left from a previous session.`;
                resumeBanner.classList.remove('d-none');
            } else {
                pendingUserId = null;
                resumeBanner.classList.add('d-none');
            }
        })
        .catch(() => {
            resumeBanner.classList.add('d-none');
        });
    }

    nameInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => checkResume(nameInput.value.trim()), 500);
    });

    // Resume: set the hidden user id so the controller resumes the session
    btnResume.addEventListener('click', () => {
        resumeIdEl.value = pendingUserId;
        document.getElementById('start-form').submit();
    });

    // Fresh start: clear resume id and submit normally
    btnFresh.addEventListener('click', () => {
        resumeIdEl.value = '';
        pendingUserId    = null;
        resumeBanner.classList.add('d-none');
        document.getElementById('start-form').submit();
    });

})();
</script>
@endpush
