<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\QuizPart;
use App\Models\Scoredif;
use App\Models\UserQuizToken;
use App\Models\UserResponse;
use App\Models\Question;
use App\Models\Userscore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    private function getNextQuiz()
    {
       //origin $userQuizToken = UserQuizToken::where("user_id", Auth::id())->with("quizpart")->latest()->first();

        //test
        $userQuizToken = UserQuizToken::where("user_id", Auth::id())
            ->with("quizpart")
            ->whereHas('quizpart', function ($query) {
                $query->where('type', 'aprontisage');
            })
            ->latest()
            ->first();

        // test



        if ($userQuizToken) {
            return QuizPart::where('PartID', '>', $userQuizToken->quiz_part_id)
                ->where("type", "aprontisage")
                ->with('questions',"essai")
                ->orderBy('PartID')
                ->first();
        } else {
            return QuizPart::where("type", "aprontisage")
                ->with('questions',"essai")
                ->orderBy('PartID')
                ->first();
        }
    }

    public function index($identify): JsonResponse
    {
        // Use the getNextQuiz function to determine the next quiz
        $nextQuizPart = $this->getNextQuiz();

        // Check if there's a next quiz part
        if ($nextQuizPart) {
            // Retrieve questions with answers for the next quiz part
            $quizPartWithQuestions = QuizPart::where('PartID', $nextQuizPart->PartID)
                 ->with(["essai",'questions.answers' => function ($query) {
                    $query->orderBy('AnswerID'); // Optional: Order answers if needed
                }])
                ->first();

            return response()->json(["scorestatuse", 'nextQuizPart' => $quizPartWithQuestions]);
        } else {
            $totalScore = Userscore::where("user_id", Auth::id())->first();

            return response()->json([
                'gamestatus'=>"finishgame1",
                'error' => 'You are  finish quizzes by ' . ($totalScore ? $totalScore->score : '0') . ' points',
                'totalScore' => $totalScore
            ],404);
        }
    }
    public function submitAnswers(Request $request): JsonResponse
    {
        $userAnswers = $request->input('useranswers');
        $answers = $userAnswers['selectedAnswers'];
        $partID = $userAnswers['quizid'];


     // return response()->json(['quizPartWithQuestions' =>$this->calculateUserNote()]);


        $userResponses = [];
        foreach ($answers as $questionId => $response) {
            $userResponses[$questionId] = [    'question_text' => Question::where('QuestionID', $questionId)->value('QuestionText'), 'answers' => [],  ];



            if (is_array($response)) {
                sort($response); // Sort the user's selected answers

                foreach ($response as $answerId) {
                    $userResponse = new UserResponse();
                    $userResponse->user_response_id = Auth::id();
                    $userResponse->question_id = $questionId;
                    $userResponse->answer_id = $answerId;
                    $userResponse->save();

                    $userResponses[$questionId]['answers'][] = [ 'answer_id' => $answerId,'answer_text' => Answer::where('AnswerID', $answerId)->value('AnswerText'), ];
                }

            }
            else {
                $userResponse = new UserResponse();
                $userResponse->user_response_id = Auth::id();
                $userResponse->question_id = $questionId;
                $userResponse->answer_id = $response;
                $userResponse->save();

                $userResponses[$questionId]['answers'][] = [ 'answer_id' => $response,  'answer_text' => Answer::where('AnswerID', $response)->value('AnswerText'),];
            }


        }


        // calculate user score
        $points= Scoredif::first()->multiple;

        $correctAnswersCount = $this->calculateUserNote();
        // calculate user score
        $userScore = Userscore::where('user_id', Auth::id())->first();

        if (!$userScore) {
            $userScore = new Userscore();
            $userScore->user_id = Auth::id();
        }

        $userScore->score =  $correctAnswersCount * $points;
        $userScore->save();
        $totalScore = $userScore->score;

        $userQuizToken = new UserQuizToken();
        $userQuizToken->user_id = Auth::id();
        $userQuizToken->quiz_part_id = $partID;
        $userQuizToken->token = true;
        $userQuizToken->save();

        $quizPartWithQuestions = QuizPart::where('PartID', $partID)
            ->with(['questions.answers' => function ($query) {
                $query->orderBy('AnswerID'); // Optional: Order answers if needed
            }])
            ->first();

        return response()->json(['quizPartWithQuestions' => $quizPartWithQuestions, 'totalScore' => $totalScore, 'userResponses' => $userResponses]);
    }





















 public function calculateUserNote() {
        // Get all the user's response answers
        $userResponses = UserResponse::where('user_response_id', Auth::id())->get();

        // Initialize a variable to keep track of the user's total score
        $userTotalScore = 0;

        // Get the distinct question IDs for the user's responses
        $distinctQuestionIds = $userResponses->pluck('question_id')->unique();

        // Iterate through each distinct question ID
        foreach ($distinctQuestionIds as $questionId) {
            // Get user's responses for this question
            $userAnswersForQuestion = $userResponses->where('question_id', $questionId)->pluck('answer_id')->toArray();

            // Get the correct answers for this question
            $correctAnswers = Answer::where('QuestionID', $questionId)
                                     ->where('IsCorrect', true)
                                     ->pluck('AnswerID')
                                     ->toArray();

            // Check if user provided all correct answers for this question
            $isQuestionCorrect = count(array_intersect($userAnswersForQuestion, $correctAnswers)) == count($correctAnswers);

            // If user selected all correct answers for this question, increment the score
            if ($isQuestionCorrect) {
                $userTotalScore++;
            }
        }

        return $userTotalScore;
    }













/// 0 usage

    public function startQuiz(): JsonResponse
    {
        response()->json("ss");
        $nextQuizPart = $this->getNextQuiz();

        if ($nextQuizPart) {
            $questions = $nextQuizPart->questions;
            return response()->json(['nextQuizPart' => $nextQuizPart, 'questions' => $questions]);
        } else {
            $totalScore = Userscore::where("user_id", Auth::id())->first();

            return response()->json(['error' => 'You are finished all aprontisa quizzes by ' . ($totalScore ? $totalScore->score : '0') . ' points']);
        }
    }

}
