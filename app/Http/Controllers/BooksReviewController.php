<?php

namespace App\Http\Controllers;

use App\BookReview;
use App\Book;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BooksReviewController extends Controller
{
    public function __construct()
    {   
        $this->middleware('auth');
    }

    public function store(Book $book, PostBookReviewRequest $request)
    {
        // @TODO implement
        DB::beginTransaction();
        try {

            $bookReview = new BookReview();
            $bookReview->review = $request->input('review');
            $bookReview->comment = $request->input('comment');

            $bookReview->user_id = Auth::id();
            // $bookReview->user_id =  $request->user_id;
            $bookReview->book_id = $book->id;
            $bookReview->save();
            DB::commit();

            return new BookReviewResource($bookReview);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['metaData' => ['code' => 500, 'message' => $e->getMessage()]], 500);
        }
    }

    public function destroy(Book $book, BookReview $review, Request $request)
    {
        // @TODO implement
        DB::beginTransaction();
        try {
            $id = $book->id;
            // dd($id);
            $review = Book::find($id);
            $review->delete();
            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['metaData' => ['code' => 500, 'message' => $e->getMessage()]], 500);
        }
    }
}
