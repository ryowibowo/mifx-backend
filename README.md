# Introduction
You are working on an online bookstore application. You have to finish a few web API methods.

The project contains functional tests. Your task is to make all the tests pass by writing the missing code.

### Setup
```hash
composer install
cp .env.example .env
php artisan db:init
php artisan migrate --seed
```

### Tests
```hash
php artisan test
```

### Requirements
An implementation of migration file for the tables is already given.
However, it is missing a part that creates many-to-many relationship between book and author.

Also, the API has to provide endpoints for four operations:

**GET** `/api/books` - a list of Books with pagination, sorting and filtering options.

Available query parameters:
- page - page number
- sortColumn - one of `title`, `avg_review` or `published_year`
- sortDirection - one of `ASC` or `DESC`
- title - search by book title
- authors - search by author’s ID (comma-separated)

Sample response (HTTP 200)

```json
{
   "data":[
      {
         "id":1,
         "isbn":"9077765476",
         "title":"Et hic et mollitia ea nihil culpa.",
         "description":"Possimus voluptatem rerum harum nemo asperiores. Consequuntur tenetur ut nemo ipsam placeat. Sunt eos cum assumenda quasi est. Dolores earum qui quod nihil commodi nisi.",
         "published_year": 2020,
         "authors":[
            {
               "id":1,
               "name":"Dr. Beth Weber PhD",
               "surname":"Jenkins"
            }
         ],
         "book_contents" : [
            {
               "id": 1,
               "label": "Sint sapiente.",
               "title": "Vel dicta nobis voluptatem.",
               "page_number": "32"
            }
         ],
         "price": 2,
         "price_rupiah": "Rp 28.000,00",
         "review":{
            "avg":4,
            "count":3
         }
      }
   ],
   "links":{
      "first":"http:\/\/localhost\/api\/books?page=1",
      "last":"http:\/\/localhost\/api\/books?page=1",
      "prev":null,
      "next":null
   },
   "meta":{
      "current_page":1,
      "from":1,
      "last_page":1,
      "path":"http:\/\/localhost\/api\/books",
      "per_page":15,
      "to":5,
      "total":5
   }
}
```

##### TODO:

- Implement `App\Http\Resources\AuthorResource::toArray` method.
- Implement `App\Http\Resources\BookResource::toArray` method.
- Implement `App\Http\Resources\BookContentResource::toArray` method.
- Implement `App\Http\Resources\BookReviewResource::toArray` method.
- Query the data from Book Eloquent model and respond with BookResource collection.
- Implement pagination feature (from Eloquent).
- Allow sorting by title.
- Allow sorting by average review.
- Allow searching by title (SQL like query).
- Allow searching by author’s ID.
- add `price_rupiah` in the response with format : `Rp 14.000,00`
  - the formula is `books.price * 14000`
  - you must create helper function named `usd_to_rupiah_format(mixed $usd)` that convert USD to Rupiah based on the formula above and return as this format : `Rp 14.000,00`

***

**POST** `/api/books` - creates a new Book resource.

Access to this endpoint requires authentication with an API token and admin privileges.

Required parameters:
- isbn - string (13 characters, digits only)
- title - string
- description - string
- authors - int[] - author’s ID
- published_year - int (between 1900 and 2020)
- price - double (in USD)

Sample response (HTTP 201)

```json
{
   "data":{
      "id":1,
      "isbn":"9788328302341",
      "title":"Clean code",
      "description":"Lorem ipsum",
      "published_year": 2020,
      "authors":[
         {
            "id":1,
            "name":"Prof. Darrin Mraz Jr.",
            "surname":"Bins"
         },
      ],
      "book_contents" : [],
      "price": 2,
      "review":{
         "avg":0,
         "count":0
      }
   }
}
```

In case of validation errors, the API should respond with the default error list from the Laravel framework and the 422 HTTP code.

##### TODO:

- Validate the required fields.
- make an api call to retreive book's contents by ISBN number.
  - third party api : https://rak-buku-api.vercel.app/api/books
  - endpoint to get book detail by ISBN Number : https://rak-buku-api.vercel.app/api/books/{isbn}. (Must use Laravel's HTTP Facade)
  - get books's contents from `details.table_of_contents` and insert to book_contents table
  - if the api return `404`, insert 1 book_contents data as defined below :
      ```JSON
      {
         "label": null,
         "title": "Cover",
         "page_number": 1
      }
      ```
  - make sure the performance is not affected by using background process (queue) to retreive data from api and inserting to book_contents table
- Ensure that the ISBN is `unique` and author’s ID `exist` in the DB.
- Store Book in the DB.
- Restrict access only for administrators with `auth.admin` middleware.
- Respond with BookResource.

***

**POST** `/api/books/{id}/reviews` - creates a new BookReview resource.

Access to this endpoint requires authentication with an API token.

Required parameters:
- review - int (1-10)
- comment - string

Sample response (HTTP 201)

```json
{
   "data":{
      "id":1,
      "review":5,
      "comment":"Lorem ipsum",
      "user":{
         "id":1,
         "name":"Kody Lebsack"
      }
   }
}
```

In case of an invalid Book ID, the API should respond with the 404 HTTP code.
In case of validation errors, the API should respond with the default error list from the Laravel framework and the 422 HTTP code.

##### TODO:

- Validate the required fields.
- Store BookReview in the DB.
- Restrict access only for `authenticated` users.
- Respond with BookReviewResource.

***

**DELETE** `/api/books/{id}/reviews/{id}` - deletes a BookReview resource.

Access to this endpoint requires authentication with an API token and admin privileges.

Sample response (HTTP 204)

```json
{}
```

In case of an invalid Book ID, the API should respond with the 404 HTTP code.

##### TODO:

- Validate if the book exists.
- Delete the book from DB.
- Return an empty body and 204 status code.

***

#### Hints

- The project is configured to use an SQLite database.
- Do not modify any tests.
- Look for comments with `@todo`.
- To make sure your answer is correct, please do a unit test or run `php artisan test`