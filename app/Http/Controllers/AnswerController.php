<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    /**
     * Record the user's answer or skip via AJAX.
     * Content-Type: application/json
     */
    public function store(Request $request)
    {
        $userId     = session('user_id');
        $questionId = (int) $request->input('question_id');
        $answerId   = $request->input('answer_id'); // null if skipped

        if (!$userId) {
            return response()->json(['error' => 'Session expired. Please restart.'], 401);
        }

        // Prevent duplicate entries (idempotent — safe for browser-resume)
        $alreadyAnswered = DB::table('results')
                             ->where('user_id', $userId)
                             ->where('question_id', $questionId)
                             ->exists();

        if ($alreadyAnswered) {
            return response()->json(['message' => 'Already recorded.']);
        }

        if ($answerId === null) {
            // User skipped
            Result::create([
                'user_id'     => $userId,
                'question_id' => $questionId,
                'answer_id'   => null,
                'status'      => 'skipped',
            ]);
        } else {
            // Check correctness using SQL — avoid loading full model
            $isCorrect = DB::table('answers')
                           ->where('id', (int) $answerId)
                           ->where('question_id', $questionId)
                           ->value('is_correct');

            Result::create([
                'user_id'     => $userId,
                'question_id' => $questionId,
                'answer_id'   => (int) $answerId,
                'status'      => $isCorrect ? 'correct' : 'wrong',
            ]);
        }

        return response()->json(['message' => 'Recorded.']);
    }
}
