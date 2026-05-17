<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    /**
     * Show the final result page.
     */
    public function index()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('welcome');
        }

        $user = User::findOrFail($userId);

        // Use SQL COUNT with conditional aggregation — satisfies aggregate function requirement
        $summary = DB::table('results')
                     ->where('user_id', $userId)
                     ->selectRaw('
                         COUNT(*) AS total,
                         SUM(CASE WHEN status = "correct" THEN 1 ELSE 0 END) AS correct,
                         SUM(CASE WHEN status = "wrong"   THEN 1 ELSE 0 END) AS wrong,
                         SUM(CASE WHEN status = "skipped" THEN 1 ELSE 0 END) AS skipped
                     ')
                     ->first();

        // Detailed breakdown per question with the chosen answer text
        $details = DB::table('results AS r')
                     ->join('questions AS q', 'q.id', '=', 'r.question_id')
                     ->leftJoin('answers AS a', 'a.id', '=', 'r.answer_id')
                     ->leftJoin('answers AS ca', function ($join) {
                         $join->on('ca.question_id', '=', 'r.question_id')
                              ->where('ca.is_correct', '=', 1);
                     })
                     ->where('r.user_id', $userId)
                     ->select(
                         'q.question_text',
                         'a.answer_text AS chosen_answer',
                         'ca.answer_text AS correct_answer',
                         'r.status'
                     )
                     ->get();

        // Clear session after viewing result
        session()->forget('user_id');

        return view('result', compact('user', 'summary', 'details'));
    }
}
