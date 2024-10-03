<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(): JsonResponse
    {
        $questions = Question::all();
        return response()->json(['questions' => $questions]);
    }



    public function store(Request $request): JsonResponse
    {
        $request->validate([ 'QuestionText' => 'required',  ]);

        $question = new Question();
        $question->QuestionText = $request->input('QuestionText');
        $question->IsBest = $request->input('IsBest') == "on" ? true : false;
        $question->quiz_id = 0;
        $question->questionfoscript = "";

        // Image Upload
        if ($request->hasFile('ImgCorrectURL')) {
            $imgUrl = $request->file('ImgCorrectURL')->storeAs('uploads', 'correct_img_' . time() . '.' . $request->file('ImgCorrectURL')->getClientOriginalExtension(), 'public');
            $question->ImgURL = 'uploads/' . basename($imgUrl);
        }

        if ($request->input('IsBest') == "on") {
            Question::where('QuestionID', '!=', $question->QuestionID)->update(['IsBest' => false]);
            $question->IsBest = true;
            $question->save();
        } else {
            $question->save();
        }
        $questions = Question::all();
        return response()->json(['message' => 'Question created successfully','questions' => $questions]);
     }





    public function update(Request $request, Question $question): JsonResponse
    {


             Question::where('QuestionID', '!=', $question->QuestionID)->update(['IsBest' => 0]);

        if($request->input('QuestionText')){
               $question->QuestionText = $request->input('QuestionText');
           }
        $question->IsBest = $request->input('IsBest') == "on" ? true : false;
        $question->save();

        $questions = Question::all();
        return response()->json(['message' => 'Question updated successfully','questions' => $questions]);
     }

    public function destroy(Question $question): JsonResponse
    {

        $question->delete();
        $questions = Question::all();
        return response()->json(['message' => 'Question deleted successfully','questions' => $questions]);

     }
}
