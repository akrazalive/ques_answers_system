<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Show the welcome / name entry page.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * AJAX: Check if a name has an incomplete quiz to resume.
     */
    public function checkResume(Request $request)
    {
        $name = trim($request->input('name', ''));

        if (empty($name)) {
            return response()->json(['resumable' => false]);
        }

        $totalQuestions = DB::table('questions')->count();

        // Find the most recent user with this name that has incomplete answers
        $user = User::where('name', $name)->latest()->first();

        if (!$user) {
            return response()->json(['resumable' => false]);
        }

        $answeredCount = DB::table('results')
                           ->where('user_id', $user->id)
                           ->count();

        // Resumable if they've answered at least one but not all
        if ($answeredCount > 0 && $answeredCount < $totalQuestions) {
            return response()->json([
                'resumable' => true,
                'user_id'   => $user->id,
                'answered'  => $answeredCount,
                'remaining' => $totalQuestions - $answeredCount,
            ]);
        }

        return response()->json(['resumable' => false]);
    }

    /**
     * Store the user name and start the quiz session.
     * If a user with the same name has an incomplete quiz, resume it.
     */
    public function store(Request $request)
    {
        $name = trim($request->input('name', ''));

        if (empty($name)) {
            return redirect()->route('welcome')->withErrors(['name' => 'Please enter your name.']);
        }

        // Check if this name has an in-progress quiz (browser-resume feature)
        $resumeId = (int) $request->input('resume_id', 0);

        if ($resumeId) {
            $user = User::find($resumeId);
            // Validate the name matches to prevent hijacking
            if ($user && strtolower($user->name) === strtolower($name)) {
                session(['user_id' => $user->id]);
                return redirect()->route('quiz.index');
            }
        }

        $user = User::create(['name' => $name]);

        // Store only user id in session (as per requirement)
        session(['user_id' => $user->id]);

        return redirect()->route('quiz.index');
    }
}
