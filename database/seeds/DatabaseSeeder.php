<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = App\User::factory(5)->create();
        $admin = App\User::factory(1)->admin()->create();

        App\Author::factory(15)->create()->each(function (App\Author $author) {
            App\Book::factory(3)->create()->each(function (App\Book $book) use ($author) {
                $book->authors()->saveMany([
                    $author,
                ]);
            });
        });

        \App\Book::all()->each(function (App\Book $book) use ($users) {
            $reviews = App\BookReview::factory(4)->make();
            $contents = App\BookContent::factory(1)->make();
            $reviews->each(function (\App\BookReview $review) use ($users) {
                $review->user()->associate($users->random());
            });
            $book->reviews()->saveMany($reviews);
            $book->bookContents()->saveMany($contents);
        });
    }
}
