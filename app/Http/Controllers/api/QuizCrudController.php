<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuizPart;
use App\Models\QuizPartQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuizCrudController extends Controller
{
    public function index()
    {
        $quizzes = QuizPart::with("essai")->withCount('questions')->get();
        return response()->json(["quizzes"=>$quizzes]);
    }



    public function store(Request $request)
    {


        $request->validate([
            'PartName' => 'required|string|max:255',
            'ImgURL' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ImgCorrectURL' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quiztype' => 'required',
        ]);

        $imgUrl = $request->file('ImgURL')->storeAs('uploads', 'img_' . time() . '.' . $request->file('ImgURL')->getClientOriginalExtension(), 'public');
        $imgCorrectUrl = $request->file('ImgCorrectURL')->storeAs('uploads', 'correct_img_' . time() . '.' . $request->file('ImgCorrectURL')->getClientOriginalExtension(), 'public');

        $quiz = QuizPart::create([
            'PartName' => $request->input('PartName'),
            'ImgURL' => 'uploads/' . basename($imgUrl),
            'ImgCorrectURL' => 'uploads/' . basename($imgCorrectUrl),
            'type' => $request->input('quiztype') ? $request->input('quiztype'): "aprontisage",
            'id_esaai' => $request->input('id_esaai') ? $request->input('id_esaai'): null,

            'identify' => Str::random(20),
        ]);
       // $quizzes = QuizPart::all();
        $quizzes = QuizPart::with("essai")->withCount('questions')->get();

        return response()->json(["message"=>'Quiz created successfully',"quizzes"=>$quizzes]);
    }

    public function update(Request $request, $quizId)
    {
        $quiz = QuizPart::find($quizId);

        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }

        $request->validate([
            'PartName' => 'string|max:255',
            'imgFile' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imgCorrectFile' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quiztype' => 'string',
        ]);

        $quizData = [];

        if ($request->has('PartName')) {
            $quizData['PartName'] = $request->input('PartName');
        }

        if ($request->has('quiztype')) {
            $quizData['type'] = $request->input('quiztype');
        }
        if ($request->has('id_esaai')!="null") {
            $quizData['id_esaai'] = $request->input('id_esaai');

        }
        if ($request->has('id_esaai')=="null") {
            $quizData['id_esaai'] = null;

        }


        if ($request->hasFile('imgFile')) {
            Storage::delete('public/' . $quiz->ImgURL);
            $imgUrl = $request->file('imgFile')->storeAs('uploads', 'img_' . time() . '.' . $request->file('imgFile')->getClientOriginalExtension(), 'public');
            $quizData['ImgURL'] = 'uploads/' . basename($imgUrl);
        }

        if ($request->hasFile('imgCorrectFile')) {
            Storage::delete('public/' . $quiz->ImgCorrectURL);
            $imgCorrectUrl = $request->file('imgCorrectFile')->storeAs('uploads', 'correct_img_' . time() . '.' . $request->file('imgCorrectFile')->getClientOriginalExtension(), 'public');
            $quizData['ImgCorrectURL'] = 'uploads/' . basename($imgCorrectUrl);
        }

        $quiz->update($quizData);

        $quizzes = QuizPart::all();
        return response()->json(["message"=>'Quiz updated successfully',"quizzes"=>$quizzes]);
    }


    public function destroy(QuizPart $quiz)
    {
        // Delete associated images
        Storage::delete(['public/' . $quiz->ImgURL, 'public/' . $quiz->ImgCorrectURL]);

        // Delete the quiz
        $quiz->delete();

        // delete all question of this quiz
        Question::where('quiz_id', $quiz->PartID)->delete();


        $quizzes = QuizPart::all();
        return response()->json(["message"=>'Quiz deleted successfully',"quizzes"=>$quizzes]);
     }





    public function associateold() {  $quizParts = QuizPart::withCount('questions')->get();   $questions = Question::all()->groupBy("QuestionText");    return response()->json(["questions"=>$questions,"quizzes"=>$quizParts]);  }
    public function associate()
    {
        $quizParts = QuizPart::withCount('questions')->get();
        $questions = Question::whereIn('QuestionID', [1, 2, 3, 4, 5, 6, 7])->get()->groupBy('QuestionText');
        return response()->json(["questions"=>$questions, "quizzes"=>$quizParts]);
    }


    public function submetassociateold(Request $request)
    {
        // Access data from the request array
        $selectedQuiz = $request->input('selectedQuiz');
        $selectedQuestions = $request->input('selectedQuestions');

        // Validate the form data
        $request->validate([
            'selectedQuiz' => 'required|exists:quiz_parts,PartID',
            'selectedQuestions' => 'required|array',
            'selectedQuestions.*' => 'exists:questions,QuestionID',
        ]);

        // Get the quiz part
        $quizPart = QuizPart::find($selectedQuiz);

        // Duplicate and associate questions with the new quiz_id
        foreach ($selectedQuestions as $questionId) {
            $originalQuestion = Question::find($questionId);

            // Duplicate the question and set the quiz_id
            $newQuestion = $originalQuestion->replicate();
            $newQuestion->quiz_id = $selectedQuiz; // Set the new quiz_id
            $newQuestion->save();

            // Attach the new question to the quiz part
            $quizPart->questions()->attach($newQuestion->QuestionID);

            // Duplicate and associate answers with the new question
            foreach ($originalQuestion->answers as $originalAnswer) {
                $newAnswer = $originalAnswer->replicate();
                $newAnswer->QuestionID = $newQuestion->QuestionID; // Set the new QuestionID
                $newAnswer->save();
            }

            // Check if the entry already exists in quiz_part_questions table
            QuizPartQuestion::firstOrCreate([
                'PartID' => $selectedQuiz,
                'QuestionID' => $newQuestion->QuestionID,
            ]);
        }

        // Return the response
        $quizParts = QuizPart::withCount('questions')->get();
        $questions = Question::all()->groupBy("QuestionText");
        return response()->json(["questions" => $questions, "quizzes" => $quizParts]);
    }



    public function submetassociate(Request $request)
    {
        // Access data from the request array
        $selectedQuiz = $request->input('selectedQuiz');
        $selectedQuestions = $request->input('selectedQuestions');

        // Validate the form data
        $request->validate([
            'selectedQuiz' => 'required|exists:quiz_parts,PartID',
            'selectedQuestions' => 'required|array',
            'selectedQuestions.*' => 'exists:questions,QuestionID',
        ]);

        // Sort the selectedQuestions array by QuestionID
        sort($selectedQuestions);

        // Get the quiz part
        $quizPart = QuizPart::find($selectedQuiz);

        // Duplicate and associate questions with the new quiz_id
        foreach ($selectedQuestions as $questionId) {
            $originalQuestion = Question::find($questionId);

            // Duplicate the question and set the quiz_id
            $newQuestion = $originalQuestion->replicate();
            $newQuestion->quiz_id = $selectedQuiz; // Set the new quiz_id
            $newQuestion->save();

            // Attach the new question to the quiz part
            $quizPart->questions()->attach($newQuestion->QuestionID);

            // Duplicate and associate answers with the new question
            foreach ($originalQuestion->answers as $originalAnswer) {
                $newAnswer = $originalAnswer->replicate();
                $newAnswer->QuestionID = $newQuestion->QuestionID; // Set the new QuestionID
                $newAnswer->save();
            }

            // Check if the entry already exists in quiz_part_questions table
            QuizPartQuestion::firstOrCreate([
                'PartID' => $selectedQuiz,
                'QuestionID' => $newQuestion->QuestionID,
            ]);
        }

        // Return the response
        $quizParts = QuizPart::withCount('questions')->get();
        $questions = Question::all()->groupBy("QuestionText");
        return response()->json(["questions" => $questions, "quizzes" => $quizParts]);
    }














    public function editanswer()
    {
        // Fetch all quizzes with their questions and answers
            $quizzesanswers = QuizPart::with('questions.answers')->get();
             return  response()->json(["quizzesanswers"=>$quizzesanswers]);
     }

    public function updateanswer(Request $request, $idanswer)
    {

       // return response()->json($idanswer);

        // Validate the form data
        $request->validate([
            'answerupdatetext' => 'required|string',
            'answerupdatetiscorrect' => 'required',
            // Add other validation rules as needed
        ]);

        $answer = Answer::find($idanswer);

        // Update the answer
        $answer->update([
            'AnswerText' =>   $request->input('answerupdatetext'),
            'IsCorrect' =>  $request->input('answerupdatetiscorrect') ,
         ]);



        $quizzesanswers = QuizPart::with('questions.answers')->get();
        return  response()->json(['message' => 'Answer updated successfully',"quizzesanswers"=>$quizzesanswers]);
      }


    public function destroyanswer($idanswer)

    {
        // Delete associated images

        // Delete the quiz
            $answer = Answer::find($idanswer);
            $answer->delete();

        $quizzesanswers = QuizPart::with('questions.answers')->get();
        return  response()->json(['message' => 'Answer deleted successfully',"quizzesanswers"=>$quizzesanswers]);

    }
}
