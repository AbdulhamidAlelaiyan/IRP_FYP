<?php

namespace Tests\Integrtion\App\Models;

use App\Models\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    protected $post;
    protected $IncorrectPost;

    protected $correct_data;
    protected $incorrect_data;

    public function setUp()
    {
        parent::setUp();
        $this->incorrect_data = [
            'isbn' =>  '1111111111',
            'user_id' => 1,
            'title' => '',
            'body' => 'Some body',
        ];
        $this->correct_data = [
            'isbn' =>  '1111111111',
            'user_id' => 1,
            'title' => 'New Post',
            'body' => 'Some body',
        ];
        $_SESSION['user_id'] = 1;
        $_POST['editordata'] = 'Some Body';
        $validateMethod = new \ReflectionMethod(Post::class, 'validate');
        $validateMethod->setAccessible(true);
        $this->post = new Post($this->correct_data);
        $this->post->delete();
        $validateMethod->invoke($this->post);
        $this->IncorrectPost = new Post($this->incorrect_data);
        $validateMethod->invoke($this->IncorrectPost);
    }

    public function testPostWillBeSavedIfDataIsCorrect()
    {
        $this->assertTrue($this->post->save());
    }

    public function testPostWillNotBeSavedIfDataIsIncorrect()
    {
        $this->assertFalse($this->IncorrectPost->save());
    }

    public function testPostWillNotBeSavedIfTitleIsEmpty()
    {
        $this->correct_data['title'] = '';
        $newPost = new Post($this->correct_data);
        $this->assertFalse($newPost->save());
    }
}
