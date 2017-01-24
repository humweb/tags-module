<?php

namespace Humweb\Tests\Tags;

use Humweb\Tests\Tags\Fake\Page;
use Humweb\Tags\Models\Tag;

class TagsTest extends TestCase
{
    protected $runMigrations = true;

    /**
     * @test
     */
    public function it_has_no_tags_yet()
    {
        $this->assertEquals(0, Tag::count());
    }

    /**
     * @test
     */
    public function it_can_tag_a_model()
    {

        $page = Page::find(1);
        $page->tag('Cool');
        $page->tag('Foo');
        $tags = $page->tagged;
        $this->assertEquals('cool', $tags[0]->slug);
        $this->assertEquals('foo', $tags[1]->slug);
    }

    /**
     * @test
     */
    public function it_can_tag_and_untag_model()
    {

        $page = Page::find(1);

        //Tag
        $page->tag('Cool');
        $page->tag('Foo');
        $this->assertEquals('cool', $page->tagged[0]->slug);

        // Untag
        $page = Page::find(1);
        $page->untag('Cool');
        $this->assertEquals(1, $page->tagged->count());

    }


    /**
     * @test
     */
    public function it_can_save_tags()
    {

        $page = Page::find(1);

        //Tag
        $page->saveTags(['Cool', 'Foo', 'Bar']);
        $this->assertEquals('cool', $page->tagged[0]->slug);
        $this->assertEquals('foo', $page->tagged[1]->slug);
        $this->assertEquals('bar', $page->tagged[2]->slug);

        // Untag
        $page = Page::find(1);
        $page->saveTags(['Foo', 'Bar']);
        $this->assertEquals(2, $page->tagged->count());
        $this->assertNull($page->tagged->where('name', 'Cool')->first());
        $this->assertEquals('foo', $page->tagged->where('name', 'Foo')->first()->slug);
    } 
    
    /**
     * @test
     */
    public function it_can_get_posts_by_tag()
    {

        // Setup
        $page = Page::find(1);
        $page->saveTags(['Cool', 'Foo', 'Bar']);
        $page2 = Page::find(2);
        $page2->saveTags(['Cool', 'Foo']);
        $page3 = Page::find(3);
        $page3->saveTags(['Cool']);

        // Assertions
        $this->assertEquals(3, Tag::count());
        $this->assertEquals(3, Page::withTag('cool')->count());
        $this->assertEquals(2, Page::withTag('foo')->count());
        $this->assertEquals(1, Page::withTag('bar')->count());

        // Test increment
        $this->assertEquals(3, Tag::select('count')->where('slug', 'cool')->value('count'));
        $this->assertEquals(2, Tag::select('count')->where('slug', 'foo')->value('count'));
        $this->assertEquals(1, Tag::select('count')->where('slug', 'bar')->value('count'));

        // Test decrement
        $page->saveTags([]);
        $this->assertEquals(2, Tag::select('count')->where('slug', 'cool')->value('count'));

    }

}
