<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuizPart;
use App\Models\Userscore;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $question = Question::select('*')->where("isBest", "1")->first();
        $identify = QuizPart::select('identify')->first();
        $totalScore = Userscore::where('user_id', Auth::id())->first();
        $quizPartsImgURL=null;
        if ($question) {
             $quizPartsImgURL = $question->quizParts[0]->ImgURL;
        } else {
        }
        $responseData = [
            'question' => $question,
            'identify' => $identify,
             'totalScore' => $totalScore ? $totalScore->score : 0,

            "quizPartsImgURL"=> $quizPartsImgURL,
        ];

        return response()->json($responseData);
    }
}
