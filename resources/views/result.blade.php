@extends('layouts.app')

@section('title', 'Your Result')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-9 col-lg-8">

        <div class="text-center mb-4">
            <div class="display-1 mb-2">🏆</div>
            <h1 class="h3 fw-bold">Quiz Complete, {{ $user->name }}!</h1>
            <p class="text-muted">Here's how you did.</p>
        </div>

        {{-- Summary stat cards --}}
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="stat-card" style="background: #16a34a;">
                    <div class="stat-number">{{ $summary->correct }}</div>
                    <div class="stat-label"><i class="bi bi-check-circle me-1"></i>Correct</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card" style="background: #dc2626;">
                    <div class="stat-number">{{ $summary->wrong }}</div>
                    <div class="stat-label"><i class="bi bi-x-circle me-1"></i>Wrong</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-card" style="background: #d97706;">
                    <div class="stat-number">{{ $summary->skipped }}</div>
                    <div class="stat-label"><i class="bi bi-skip-forward me-1"></i>Skipped</div>
                </div>
            </div>
        </div>

        {{-- Score bar --}}
        @php
            $total = $summary->total ?: 1;
            $pct   = round(($summary->correct / $total) * 100);
        @endphp
        <div class="card p-3 mb-4">
            <div class="d-flex justify-content-between small fw-semibold mb-1">
                <span>Score</span>
                <span>{{ $summary->correct }} / {{ $summary->total }}</span>
            </div>
            <div class="progress" style="height:12px;">
                <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: {{ $pct }}%; background: #16a34a;"
                    aria-valuenow="{{ $pct }}"
                    aria-valuemin="0"
                    aria-valuemax="100">
                </div>
            </div>
            <div class="text-end small text-muted mt-1">{{ $pct }}%</div>
        </div>

        {{-- Detailed breakdown --}}
        <div class="card p-4 mb-4">
            <h2 class="h6 fw-bold mb-3">Question Breakdown</h2>

            @foreach ($details as $i => $row)
                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-start gap-2 mb-1">
                        <span class="badge rounded-pill
                            @if($row->status === 'correct')  bg-success
                            @elseif($row->status === 'wrong') bg-danger
                            @else                             bg-warning text-dark
                            @endif
                            mt-1">
                            {{ ucfirst($row->status) }}
                        </span>
                        <p class="mb-0 fw-semibold">{{ $i + 1 }}. {{ $row->question_text }}</p>
                    </div>

                    @if ($row->status === 'skipped')
                        <p class="mb-0 small text-muted ms-5">
                            You skipped this question.
                            <span class="text-success fw-semibold">Correct: {{ $row->correct_answer }}</span>
                        </p>
                    @elseif ($row->status === 'wrong')
                        <p class="mb-0 small ms-5">
                            <span class="text-danger">Your answer: {{ $row->chosen_answer }}</span>
                            &nbsp;·&nbsp;
                            <span class="text-success fw-semibold">Correct: {{ $row->correct_answer }}</span>
                        </p>
                    @else
                        <p class="mb-0 small text-success ms-5">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $row->chosen_answer }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a href="{{ route('welcome') }}" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-arrow-repeat me-1"></i> Play Again
            </a>
        </div>

    </div>
</div>
@endsection
