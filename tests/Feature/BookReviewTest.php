<?php

namespace Tests\Feature;

use App\Book;
use App\BookReview;
use App\User;
use Tests\TestCase;

class BookReviewTest extends TestCase
{
    public function testDenyGuestAccess()
    {
        $book = Book::factory()->createOne();

        $response = $this->postJson($this->postUrl($book->id), [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ]);

        $response->assertStatus(401);
    }

    public function testError404OnInvalidBookId()
    {
        $user = User::factory()->admin()->createOne();

        $response = $this
            ->actingAs($user)
            ->postJson($this->postUrl(99999), [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ]);

        $response->assertStatus(404);
    }

    public function testSuccessfulPost()
    {
        $user = User::factory()->admin()->createOne();
        $book = Book::factory()->createOne();

        $response = $this
            ->actingAs($user)
            ->postJson($this->postUrl($book->id), [
                'review' => 5,
                'comment' => 'Lorem ipsum',
            ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');
        $bookReview = BookReview::find($id);

        $response->assertJson([
            'data' => [
                'id' => $bookReview->id,
                'review' => $bookReview->review,
                'comment' => $bookReview->comment,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ],
        ]);
        $this->assertEquals(5, $bookReview->review);
        $this->assertEquals('Lorem ipsum', $bookReview->comment);
        $this->assertEquals($book->id, $bookReview->book->id);
        $this->assertEquals($user->id, $bookReview->user->id);
    }

    public function testSuccessfulDelete()
    {
        $user = User::factory()->admin()->createOne();
        $book = Book::factory()->createOne();
        $review = BookReview::factory()->createOne([
            'book_id' => $book->id,
            'user_id' => $user->id
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson($this->deleteUrl($book->id, $review->id), []);

        $response->assertStatus(204);
    }

    public function test404NotFoundDelete()
    {
        $user = User::factory()->admin()->createOne();

        $response = $this
            ->actingAs($user)
            ->deleteJson($this->deleteUrl(99999, 99999), []);

        $response->assertStatus(404);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidation(array $invalidData, string $invalidParameter)
    {
        $book = Book::factory()->createOne(['isbn' => '9788328302341']);
        $user = User::factory()->admin()->createOne();

        $validData = [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ];
        $data = array_merge($validData, $invalidData);

        $response = $this
            ->actingAs($user)
            ->postJson($this->postUrl($book->id), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$invalidParameter]);
    }

    public function validationDataProvider()
    {
        return [
            [['review' => null], 'review'],
            [['review' => ''], 'review'],
            [['review' => 0], 'review'],
            [['review' => 11], 'review'],
            [['review' => 3.5], 'review'],
            [['review' => []], 'review'],
            [['comment' => null], 'comment'],
            [['comment' => ''], 'comment'],
            [['comment' => []], 'comment'],
        ];
    }

    private function postUrl(int $bookId)
    {
        return sprintf('/api/books/%d/reviews', $bookId);
    }

    private function deleteUrl(int $bookId, int $reviewId)
    {
        return sprintf('/api/books/%d/reviews/%d', $bookId, $reviewId);
    }
}
