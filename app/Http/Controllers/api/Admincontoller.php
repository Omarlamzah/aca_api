<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\QuizPart;
use App\Models\Scoredif;
use App\Models\User;
use App\Models\UserQuizToken;
use App\Models\UserResponse;
use App\Models\Userscore;
use App\Notifications\AccountActivated;
use Carbon\Carbon;
use http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;



class Admincontoller extends Controller
{
    public function index()
    {
        return redirect()->route("quizzes.index");
    }

    public function allusers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function activateUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->isactive = !$user->isactive;
        $user->save();

        Notification::send($user, new AccountActivated($user));



        return response()->json(['message' => 'User account has been activated.',"users"=>User::all()]);
    }

    public function adminateuser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->isadmin = !$user->isadmin;
        $user->save();
        //Notification::send($user, new AccountActivated($user));

        return response()->json(['message' => 'User account has been changed admin satus.',"users"=>User::all()]);
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User has been deleted.']);
    }

    public function userscores()
    {
        // Fetch all scores, including associated user information
        $userScores = Userscore::with('user')
            ->orderByDesc('created_at') // Order by created_at to get the latest date
            ->get();

        // Get the latest date from the scores
        $latestDate = $userScores->first()->created_at->format('Y-m-d');

        // Filter scores that match the latest date
        $latestScores = $userScores->filter(function ($score) use ($latestDate) {
            return $score->created_at->format('Y-m-d') === $latestDate;
        });

        // Prepare data for the chart
        $userNames = $latestScores->pluck('user.name')->toArray();
        $userScoresValues = $latestScores->pluck('score')->toArray();

        return response()->json([
            'userScores' => $latestScores,
            'userNames' => $userNames,
            'userScoresValues' => $userScoresValues,
        ]);
    }













    public function myscore()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $userscore = Userscore::where('user_id', $user->id)->first();

        if (!$userscore) {
            return response()->json(['error' => 'Score not found for the user'], 404);
        }

        $quiztokenstatus = "";
        $totlaquiz = QuizPart::count();
        $totlaquiztoken = UserQuizToken::where("user_id", Auth::id())->count();

        if ($totlaquiz == $totlaquiztoken) {
            $quiztokenstatus = "allquiztoken";
        }
        $score = $userscore->score ? $userscore->score  :0;
        $scoredif = Scoredif::select("scoredif")->first();
        $getscore = $scoredif->scoredif ? $scoredif->scoredif :0;
        return response()->json(['user_score' => $score,"scoredif"=> $getscore,"quiztokenstatus"=>$quiztokenstatus]);
    }



    public function  points (){
        $scoredif = Scoredif::first();
        return response()->json( $scoredif);
    }
    public function submitpoints(\Illuminate\Http\Request $data)
    {
        // Assuming you have 'scoredif' and 'multiple' keys in the request data
        $scoredifValue = $data->input('scoredif');
        $multipleValue = $data->input('multiple');

        $scoredif = Scoredif::first();
        // Check if new values are passed and update them
        if ($scoredifValue !== "") {
            $scoredif->scoredif = $scoredifValue;
        }

        if ($multipleValue !== null) {
            $scoredif->multiple = $multipleValue;
        }
        $scoredif->save();
        $scoredif = Scoredif::first();

        // Here reset users Notes
        $allUsers = User::all("id");
        foreach ($allUsers as $user) {
            $totalScore = $this->calculateUserNoteReset($user->id);
            $userScore = Userscore::where('user_id', $user->id)->first();

            if (!$userScore) {
                $userScore = new Userscore();
                $userScore->user_id = $user->id;
            }

            $points = Scoredif::first()->multiple;
            $userScore->score = $totalScore * $points;

            $userScore->save();
        }
// Here reset users Notes


        return response()->json( $scoredif);
    }












    /// reset all users Notes
    public function calculateUserNoteReset($id) {
        // Get all the user's response answers
        $userResponses = UserResponse::where('user_response_id',$id)->get();

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















}




