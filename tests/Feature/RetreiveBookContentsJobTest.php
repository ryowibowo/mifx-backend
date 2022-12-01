<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Jobs\RetreiveBookContentsJob;

class RetreiveBookContentsJobTest extends TestCase
{
    /**
     * test job is making call to third party api
     *
     * @return void
     */
    public function testJobMakeCallToThirdPartyApi()
    {
        $this->mockSuccessHttpApi();
        $book = Book::factory()->createOne();
        $job = new RetreiveBookContentsJob($book);
        $job->handle();

        $this->assertCount(1, Http::recorded());
    }

    /**
     * test job can retreive data from third api 
     * and insert to database
     *
     * @return void
     */
    public function testJobCanInsertTableOfContentsToBookContents()
    {
        $this->mockSuccessHttpApi();
        $book = Book::factory()->createOne();
        $job = new RetreiveBookContentsJob($book);
        $job->handle();
        $book->load('bookContents');
        $contents = $book->bookContents;
        $this->assertCount(11, $contents);
    }

    /**
     * test job can retreive data from third api 
     * and insert to database
     *
     * @return void
     */
    public function testJobInsertDefaultBookContentsIfThirdPartyResponseIsNotFound()
    {
        $this->mockNotFoundHttpApi();
        $book = Book::factory()->createOne();
        $job = new RetreiveBookContentsJob($book);
        $job->handle();
        $book->load('bookContents');
        $contents = $book->bookContents ?? [];
        $this->assertCount(1, $contents);
    }
}
