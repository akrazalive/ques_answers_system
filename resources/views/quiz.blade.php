@extends('layouts.app')

@section('title', 'Quiz — Question')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-7">

        {{-- Progress bar --}}
        <div class="mb-3">
            @php
                $total     = $totalQuestions;
                $done      = $total - $remaining;
                $pct       = $total > 0 ? round(($done / $total) * 100) : 0;
            @endphp
            <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Question <strong id="q-done">{{ $done + 1 }}</strong> of <strong>{{ $total }}</strong></span>
                <span id="q-remaining">{{ $remaining }} remaining</span>
            </div>
            <div class="progress">
                <div
                    class="progress-bar"
                    id="progress-bar"
                    role="progressbar"
                    style="width: {{ $pct }}%"
                    aria-valuenow="{{ $pct }}"
                    aria-valuemin="0"
                    aria-valuemax="100">
                </div>
            </div>
        </div>

        {{-- Question card --}}
        <div class="card p-4" id="question-card">

            <h2 class="h5 fw-bold mb-4" id="question-text">
                {{ $question->question_text }}
            </h2>

            <form id="quiz-form">
                @csrf
                <input type="hidden" id="question-id" value="{{ $question->id }}">

                <div id="answers-container">
                    @foreach ($question->answers as $answer)
                        <label class="answer-option" for="ans-{{ $answer->id }}">
                            <input
                                type="radio"
                                name="answer_id"
                                id="ans-{{ $answer->id }}"
                                value="{{ $answer->id }}"
                            >
                            <span>{{ $answer->answer_text }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="button" id="btn-skip" class="btn btn-outline-secondary flex-fill">
                        <i class="bi bi-skip-forward me-1"></i> Skip
                    </button>
                    <button type="button" id="btn-next" class="btn btn-primary flex-fill">
                        Next <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>

        </div>

        {{-- Feedback toast --}}
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="feedback-toast" class="toast align-items-center border-0" role="alert" aria-live="assertive">
                <div class="d-flex">
                    <div class="toast-body" id="toast-message"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const ANSWER_URL = '{{ route("answer.store") }}';
    const NEXT_URL   = '{{ route("quiz.next") }}';
    const RESULT_URL = '{{ route("result.index") }}';

    const totalQuestions = {{ $totalQuestions }};
    let   doneCount      = {{ $done }};

    // ── Helpers ──────────────────────────────────────────────────────────────

    function showLoading(on) {
        document.getElementById('loading-overlay').classList.toggle('active', on);
    }

    function showToast(message, type) {
        const toast    = document.getElementById('feedback-toast');
        const msgEl    = document.getElementById('toast-message');
        msgEl.textContent = message;
        toast.className = `toast align-items-center border-0 text-bg-${type}`;
        bootstrap.Toast.getOrCreateInstance(toast, { delay: 1800 }).show();
    }

    function ajaxPost(url, data) {
        return fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(data),
        }).then(r => r.json());
    }

    // ── Render a new question ─────────────────────────────────────────────────

    function renderQuestion(data) {
        document.getElementById('question-id').value   = data.question.id;
        document.getElementById('question-text').textContent = data.question.text;

        const container = document.getElementById('answers-container');
        container.innerHTML = '';

        data.answers.forEach(ans => {
            const label = document.createElement('label');
            label.className = 'answer-option';
            label.htmlFor   = `ans-${ans.id}`;
            label.innerHTML = `
                <input type="radio" name="answer_id" id="ans-${ans.id}" value="${ans.id}">
                <span>${ans.answer_text}</span>
            `;
            container.appendChild(label);
        });

        // Re-attach highlight listeners
        attachOptionListeners();

        // Update progress
        doneCount++;
        const pct = Math.round((doneCount / totalQuestions) * 100);
        document.getElementById('progress-bar').style.width = pct + '%';
        document.getElementById('q-done').textContent       = doneCount + 1;
        document.getElementById('q-remaining').textContent  = data.remaining + ' remaining';
    }

    // ── Highlight selected option ─────────────────────────────────────────────

    function attachOptionListeners() {
        document.querySelectorAll('.answer-option').forEach(label => {
            label.addEventListener('click', () => {
                document.querySelectorAll('.answer-option').forEach(l => l.classList.remove('selected'));
                label.classList.add('selected');
            });
        });
    }

    // ── Submit answer then load next ──────────────────────────────────────────

    function submitAndAdvance(answerId) {
        const questionId = document.getElementById('question-id').value;

        showLoading(true);

        ajaxPost(ANSWER_URL, { question_id: questionId, answer_id: answerId })
            .then(() => ajaxPost(NEXT_URL, {}))
            .then(data => {
                showLoading(false);

                if (data.finished) {
                    window.location.href = RESULT_URL;
                    return;
                }

                renderQuestion(data);

                if (answerId === null) {
                    showToast('Question skipped.', 'warning');
                }
            })
            .catch(() => {
                showLoading(false);
                showToast('Something went wrong. Please try again.', 'danger');
            });
    }

    // ── Button handlers ───────────────────────────────────────────────────────

    document.getElementById('btn-next').addEventListener('click', () => {
        const selected = document.querySelector('input[name="answer_id"]:checked');
        if (!selected) {
            showToast('Please select an answer or use Skip.', 'warning');
            return;
        }
        submitAndAdvance(selected.value);
    });

    document.getElementById('btn-skip').addEventListener('click', () => {
        submitAndAdvance(null);
    });

    // ── Init ──────────────────────────────────────────────────────────────────
    attachOptionListeners();

})();
</script>
@endpush
