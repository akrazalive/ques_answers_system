<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Seed 5 geography / current affairs questions with 4 answers each.
     * One answer per question is marked correct (is_correct = 1).
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'What is the currency of Brazil?',
                'answers'  => [
                    ['text' => 'Peso',   'correct' => false],
                    ['text' => 'Real',   'correct' => true],
                    ['text' => 'Dollar', 'correct' => false],
                    ['text' => 'Euro',   'correct' => false],
                ],
            ],
            [
                'question' => 'What is the capital city of Qatar?',
                'answers'  => [
                    ['text' => 'Dubai',  'correct' => false],
                    ['text' => 'Muscat', 'correct' => false],
                    ['text' => 'Doha',   'correct' => true],
                    ['text' => 'Manama', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which country hosted the FIFA World Cup 2022?',
                'answers'  => [
                    ['text' => 'UAE',          'correct' => false],
                    ['text' => 'Saudi Arabia', 'correct' => false],
                    ['text' => 'Qatar',        'correct' => true],
                    ['text' => 'Bahrain',      'correct' => false],
                ],
            ],
            [
                'question' => 'What is the largest continent by area?',
                'answers'  => [
                    ['text' => 'Africa',       'correct' => false],
                    ['text' => 'North America','correct' => false],
                    ['text' => 'Europe',       'correct' => false],
                    ['text' => 'Asia',         'correct' => true],
                ],
            ],
            [
                'question' => 'Which river is the longest in the world?',
                'answers'  => [
                    ['text' => 'Amazon',  'correct' => false],
                    ['text' => 'Nile',    'correct' => true],
                    ['text' => 'Yangtze', 'correct' => false],
                    ['text' => 'Congo',   'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $item) {
            $questionId = DB::table('questions')->insertGetId([
                'question_text' => $item['question'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($item['answers'] as $answer) {
                DB::table('answers')->insert([
                    'question_id' => $questionId,
                    'answer_text' => $answer['text'],
                    'is_correct'  => $answer['correct'] ? 1 : 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
