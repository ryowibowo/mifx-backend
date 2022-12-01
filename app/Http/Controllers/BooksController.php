<?php

namespace App\Http\Controllers;

use App\Book;
use App\Author;
use App\BookAuthor;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // @TODO implement
        
        $input = $request->all();
        $book = Book::query();

        if (!empty($input['title']))
            $book->where('title','like','%'.$input['title'].'%');

        if (isset($input['authors'])) {
            $authors = (strpos($input['authors'],',')!== false ) ? explode(',',$input['authors'] ) : [$input['authors']];
            $book->whereHas('authors', function($q)use ($authors){
                $q->whereIn('author_id', $authors );
            });

        }

        if (isset($input['sortColumn'])) {


            $sortDirection = (isset($input['sortDirection'])&& strtoupper( $input['sortDirection']) == 'DESC')?  'DESC': 'asc';


            if ($input['sortColumn']=='avg_review')
            {
                $book->withCount(['reviews as review_average' => function($query) {
                    $query->select(DB::raw('coalesce(avg(review),0)'));
                }])->orderBy('review_average',$sortDirection);

            }
            else {

                $book->OrderBy( $input['sortColumn'],$sortDirection );
            }
        }

        return BookResource::collection($book->paginate() );
    }

    public function store(PostBookRequest $request)
    {
        // dd($request->all());
        // @TODO implement
        DB::beginTransaction();
        try {

            $input = $request->all();

            $book = new Book();
            $book->title = $input['title'];
            $book->isbn = $input['isbn'];
            $book->description = $input['description'];
            $book->published_year = $input['published_year'];
            $book->save();

            $book->authors()->attach($input['authors']);

            DB::commit();

            return new BookResource($book);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['metaData' => ['code' => 500, 'message' => $e->getMessage()]], 500);
        }
    }
}
