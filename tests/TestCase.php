<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        TestResponse::macro('assertViewIs', function ($name) {
            return Assert::assertEquals($name, $this->original->name());
        });

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        EloquentCollection::macro('assertContains', function ($value) {
            return Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contained the specified value');
        });

        EloquentCollection::macro('assertNotContains', function ($value) {
            return Assert::assertFalse($this->contains($value), 'Failed asserting that the collection did not contain the specified value');
        });

        EloquentCollection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items));
            $this->zip($items)->each(function ($pair) {
                list($a, $b) = $pair;
                Assert::assertTrue($a->is($b));
            });
        });
    }

    protected function fromUrl($url)
    {
        session()->setPreviousUrl(url($url));

        return $this;
    }
}
