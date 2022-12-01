<?php

namespace Tests;

use App\Book;
use App\Author;
use App\BookContent;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function bookToResourceArray(Book $book)
    {
        return [
            'id' => $book->id,
            'isbn' => $book->isbn,
            'title' => $book->title,
            'description' => $book->description,
            'price' => $book->price,
            'price_rupiah' => usd_to_rupiah_format($book->price),
            'authors' => $book->authors->map(function (Author $author) {
                return ['id' => $author->id, 'name' => $author->name, 'surname' => $author->surname];
            })->toArray(),
            'book_contents' => $book->bookContents->map(function (BookContent $bookContent) {
                return ['id' => $bookContent->id, 'title' => $bookContent->title, 'label' => $bookContent->label, 'page_number' => $bookContent->page_number];
            })->toArray(),
            'review' => [
                'avg' => (int) round($book->reviews->avg('review')),
                'count' => (int) $book->reviews->count(),
            ],
        ];
    }

    /**
     * mock http request to retrive books detail
     *
     * @return void
     */
    protected function mockSuccessHttpApi()
    {
        $data = <<<JSON
                {
                    "info_url": "https://openlibrary.org/books/OL25416599M/Why_Don't_Penguins'_Feet_Freeze",
                    "preview": "borrow",
                    "preview_url": "https://archive.org/details/whydontpenguinsf00ohar_0",
                    "thumbnail_url": "https://covers.openlibrary.org/b/id/7240187-S.jpg",
                    "details": {
                        "publishers": [
                        "Profile Books"
                        ],
                        "table_of_contents": [
                        {
                            "level": 0,
                            "label": "",
                            "title": "Introduction",
                            "pagenum": "1"
                        },
                        {
                            "level": 0,
                            "label": "1",
                            "title": "Our bodies",
                            "pagenum": "3"
                        },
                        {
                            "level": 0,
                            "label": "2",
                            "title": "Feeling OK?",
                            "pagenum": "33"
                        },
                        {
                            "level": 0,
                            "label": "3",
                            "title": "Plants and animals",
                            "pagenum": "47"
                        },
                        {
                            "level": 0,
                            "label": "4",
                            "title": "Food and drink",
                            "pagenum": "77"
                        },
                        {
                            "level": 0,
                            "label": "5",
                            "title": "Domestic science",
                            "pagenum": "119"
                        },
                        {
                            "level": 0,
                            "label": "6",
                            "title": "Our planet, our universe",
                            "pagenum": "145"
                        },
                        {
                            "level": 0,
                            "label": "7",
                            "title": "Weird weather",
                            "pagenum": "167"
                        },
                        {
                            "level": 0,
                            "label": "8",
                            "title": "Troublesome transport",
                            "pagenum": "175"
                        },
                        {
                            "level": 0,
                            "label": "9",
                            "title": "Best of the rest",
                            "pagenum": "199"
                        },
                        {
                            "level": 0,
                            "label": "",
                            "title": "Index",
                            "pagenum": "233"
                        }
                        ],
                        "contributors": [
                        {
                            "role": "Editor",
                            "name": "Mick O'Hare"
                        },
                        {
                            "role": "Publisher",
                            "name": "Profile Books Ltd, 3A Exmouth House, Pine Street, Exmouth Market, London EC1R OJH"
                        },
                        {
                            "role": "Copyright",
                            "name": "New Scientist"
                        },
                        {
                            "role": "Text Design",
                            "name": "Sue Lamble"
                        },
                        {
                            "role": "Typesetter",
                            "name": "MacGuru Ltd"
                        },
                        {
                            "role": "Printer",
                            "name": "Bookmarque Ltd, Croydon, Surrey, Great Britain"
                        },
                        {
                            "role": "Designer",
                            "name": "Bob Eames"
                        },
                        {
                            "role": "Cover Art and Illustrations",
                            "name": "Brett Ryder"
                        }
                        ],
                        "covers": [
                        7240187
                        ],
                        "physical_format": "Paperback",
                        "key": "/books/OL25416599M",
                        "ocaid": "whydontpenguinsf00ohar_0",
                        "publish_places": [
                        "London"
                        ],
                        "isbn_13": [
                        "9788328302341"
                        ],
                        "pagination": "[4], 236 p. ; 20 cm.",
                        "source_records": [
                        "ia:whydontpenguinsf00ohar_465",
                        "ia:whydontpenguinsf00ohar_0",
                        "marc:marc_loc_2016/BooksAll.2016.part36.utf8:176412601:735"
                        ],
                        "subtitle": "and 114 other questions",
                        "title": "Why Don't Penguins' Feet Freeze?",
                        "notes": {
                        "type": "/type/text",
                        "value": "Includes index."
                        },
                        "number_of_pages": 240,
                        "languages": [
                        {
                            "key": "/languages/eng"
                        }
                        ],
                        "isbn_10": [
                        "1861978766"
                        ],
                        "publish_date": "2006",
                        "copyright_date": "2006",
                        "by_statement": "More questions and answers from the popular 'Last Word' column; edited by Mick O'Hare",
                        "works": [
                        {
                            "key": "/works/OL15171670W"
                        }
                        ],
                        "type": {
                        "key": "/type/edition"
                        },
                        "physical_dimensions": "19.8 x 12.7 x 1.6 centimeters",
                        "lccn": [
                        "2009293161"
                        ],
                        "lc_classifications": [
                        "Q173 .W624 2006"
                        ],
                        "latest_revision": 8,
                        "revision": 8,
                        "created": {
                        "type": "/type/datetime",
                        "value": "2012-10-15T15:57:26.557420"
                        },
                        "last_modified": {
                        "type": "/type/datetime",
                        "value": "2020-12-23T06:13:04.277327"
                        }
                    },
                    "id": "9788328302341",
                    "isbn": "9788328302341"
                }
                JSON;

        Http::fake([
            'https://rak-buku-api.vercel.app/api/books*' => Http::response([
                'status' => 'success',
                'message' => 'OK',
                'data' => json_decode($data, true)
            ], 200),
            '*' => Http::response([
                'status' => 'error',
                'message' => 'Server Erorr',
                'data' => null
            ], 500),
        ]);
    }
    /**
     * mock http request to retrive books detail
     *
     * @return void
     */
    protected function mockNotFoundHttpApi()
    {
        $data = <<<JSON
                {
                    "status": "error",
                    "message": "not found",
                    "data": null
                }
                JSON;

        Http::fake([
            'https://rak-buku-api.vercel.app/api/books*' => Http::response([
                'status' => 'error',
                'message' => 'not found',
                'data' => null
            ], 404)
        ]);
    }
}
