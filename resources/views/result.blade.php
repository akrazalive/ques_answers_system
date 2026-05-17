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
            $total      = $summary->total ?: 1;
            $pct        = round(($summary->correct / $total) * 100);
            $marksScore = $summary->correct * 10;
            $maxScore   = $total * 10;
        @endphp
        <div class="card p-3 mb-4">
            <div class="d-flex justify-content-between small fw-semibold mb-1">
                <span>Score</span>
                <span>{{ $marksScore }} / {{ $maxScore }} pts &nbsp;·&nbsp; {{ $summary->correct }} correct of {{ $summary->total }}</span>
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

        {{-- Leaderboard --}}
        @if ($leaderboard->isNotEmpty())
        <div class="card p-4 mb-4">
            <h2 class="h6 fw-bold mb-3">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Leaderboard — Recent Players
            </h2>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:2.5rem">#</th>
                            <th>Name</th>
                            <th class="text-center text-success">Correct</th>
                            <th class="text-center text-danger">Wrong</th>
                            <th class="text-center text-warning">Skipped</th>
                            <th class="text-center">Total Q</th>
                            <th class="text-center">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaderboard as $rank => $row)
                            <tr class="{{ $row->name === $user->name ? 'table-primary fw-semibold' : '' }}">
                                <td>
                                    @if ($rank === 0)
                                        <span title="1st">🥇</span>
                                    @elseif ($rank === 1)
                                        <span title="2nd">🥈</span>
                                    @elseif ($rank === 2)
                                        <span title="3rd">🥉</span>
                                    @else
                                        {{ $rank + 1 }}
                                    @endif
                                </td>
                                <td>
                                    {{ $row->name }}
                                    @if ($row->name === $user->name)
                                        <span class="badge bg-primary ms-1" style="font-size:.7rem;">You</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill">{{ $row->correct }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger rounded-pill">{{ $row->wrong }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $row->skipped }}</span>
                                </td>
                                <td class="text-center text-muted">{{ $row->total }}</td>
                                <td class="text-center">
                                    <span class="fw-bold" style="color: var(--primary);">{{ $row->score }}</span>
                                    <span class="text-muted small"> pts</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-2 mb-0">
                <i class="bi bi-info-circle me-1"></i>Each correct answer = 10 points. Only completed quizzes are shown.
            </p>
        </div>
        @endif

        <div class="text-center">
            <a href="{{ route('welcome') }}" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-arrow-repeat me-1"></i> Play Again
            </a>
        </div>

    </div>
</div>
@endsection
