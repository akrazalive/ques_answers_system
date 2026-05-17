<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Show the quiz page (initial question load — non-AJAX as per exemption).
     */
    public function index()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('welcome');
        }

        // Get total question count using SQL COUNT aggregate
        $totalQuestions = Question::count();

        // Get IDs of questions already answered/skipped by this user
        $answeredIds = Result::where('user_id', $userId)
                             ->pluck('question_id')
                             ->toArray();

        $remaining = $totalQuestions - count($answeredIds);

        // If all questions done, redirect to result
        if ($remaining === 0) {
            return redirect()->route('result.index');
        }

        // Pick a random unanswered question for initial load
        $question = Question::with('answers')
                            ->whereNotIn('id', $answeredIds)
                            ->inRandomOrder()
                            ->first();

        return view('quiz', compact('question', 'totalQuestions', 'answeredIds', 'remaining'));
    }

    /**
     * Return the next random unanswered question via AJAX.
     */
    public function nextQuestion(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['error' => 'Session expired. Please restart.'], 401);
        }

        // Get IDs already answered/skipped using a subquery (SQL-efficient)
        $answeredIds = DB::table('results')
                         ->where('user_id', $userId)
                         ->pluck('question_id')
                         ->toArray();

        // Pick a random unanswered question using SQL RAND()
        $question = DB::table('questions')
                      ->whereNotIn('id', $answeredIds)
                      ->inRandomOrder()
                      ->first();

        if (!$question) {
            return response()->json(['finished' => true]);
        }

        // Fetch answers for this question
        $answers = DB::table('answers')
                     ->where('question_id', $question->id)
                     ->get(['id', 'answer_text']);

        // Use SQL COUNT to get remaining count
        $remaining = DB::table('questions')
                       ->whereNotIn('id', $answeredIds)
                       ->count();

        return response()->json([
            'finished'  => false,
            'question'  => [
                'id'   => $question->id,
                'text' => $question->question_text,
            ],
            'answers'   => $answers,
            'remaining' => $remaining,
        ]);
    }
}
