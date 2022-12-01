<?php

namespace Tests\Feature;

use App\Author;
use App\Book;
use App\Jobs\RetreiveBookContentsJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BookPostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Bus::fake();
    }

    public function testDenyGuestAccess()
    {
        $author = Author::factory()->createOne();

        $response = $this->postJson('/api/books', [
            'isbn' => '9788328302341',
            'title' => 'PHP for beginners',
            'description' => 'Lorem ipsum',
            'authors' => [$author->id],
            'price' => '12.25',
        ]);

        $response->assertStatus(401);
    }

    public function testDenyNonAdminUserAccess()
    {
        $user = User::factory()->createOne();
        $author = Author::factory()->createOne();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/books', [
                'isbn' => '9788328302341',
                'title' => 'Clean code',
                'description' => 'Lorem ipsum',
                'authors' => [$author->id],
                'price' => '12.25',
            ]);

        $response->assertStatus(403);
    }

    public function testSuccessfulPost()
    {
        $user = User::factory()->admin()->createOne();
        $author = Author::factory()->createOne();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/books', [
                'isbn' => '9788328302341',
                'title' => 'Clean code',
                'description' => 'Lorem ipsum',
                'authors' => [$author->id],
                'published_year' => 2000,
                'price' => '12.25',
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');
        $book = Book::find($id);
        $this->assertResponseContainsBook($response, $book);
        $this->assertEquals('9788328302341', $book->isbn);
        $this->assertEquals('Clean code', $book->title);
        $this->assertEquals('Lorem ipsum', $book->description);
        $this->assertEquals(2000, $book->published_year);
        $this->assertEquals($author->id, $book->authors[0]->id);
    }

    public function testSuccessfulPostShouldDispatchRetreiveBookContentsJob()
    {
        $user = User::factory()->admin()->createOne();
        $author = Author::factory()->createOne();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/books', [
                'isbn' => '9788328302341',
                'title' => 'Clean code',
                'description' => 'Lorem ipsum',
                'authors' => [$author->id],
                'published_year' => 2000,
                'price' => '12.25',
            ]);

        $response->assertStatus(201);
        Bus::assertDispatched(RetreiveBookContentsJob::class);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidation(array $invalidData, string $invalidParameter)
    {
        $book = Book::factory()->createOne(['isbn' => '9788328302341']);
        $user = User::factory()->admin()->createOne();
        $authors = Author::factory(2)->create();
        $authorIds = $authors->pluck('id');

        $validData = [
            'isbn' => '9788328347786',
            'title' => 'Book title',
            'description' => 'Lorem ipsum',
            'authors' => $authorIds,
            'published_year' => 2010,
            'price' => '12.25',
        ];
        $data = array_merge($validData, $invalidData);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/books', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$invalidParameter]);
    }

    public function validationDataProvider()
    {
        return [
            [['price' => null], 'price'],
            [['price' => ''], 'price'],
            [['isbn' => null], 'isbn'],
            [['isbn' => ''], 'isbn'],
            [['isbn' => '9788328302341'], 'isbn'],
            [['isbn' => '978832830234'], 'isbn'],
            [['isbn' => '97883283023422'], 'isbn'],
            [['isbn' => []], 'isbn'],
            [['isbn' => [673890]], 'isbn'],
            [['isbn' => ['978832830234']], 'isbn'],
            [['isbn' => 'FCKGWRHQQ2'], 'isbn'],
            [['title' => null], 'title'],
            [['title' => ''], 'title'],
            [['title' => []], 'title'],
            [['description' => null], 'description'],
            [['description' => ''], 'description'],
            [['description' => []], 'description'],
            [['authors' => null], 'authors'],
            [['authors' => []], 'authors'],
            [['authors' => ''], 'authors'],
            [['authors' => 1], 'authors'],
            [['authors' => [999999]], 'authors.0'],
            [['authors' => [[]]], 'authors.0'],
            [['authors' => [null]], 'authors.0'],
            [['published_year' => 1899], 'published_year'],
            [['published_year' => 2022], 'published_year'],
            [['published_year' => null], 'published_year']
        ];
    }

    private function assertResponseContainsBook(TestResponse $response, Book $book): void
    {
        $response->assertJson([
            'data' => $this->bookToResourceArray($book),
        ]);
    }
}
