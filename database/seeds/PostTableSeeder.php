<?php

use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $post = new App\Post([
          'title' => 'Laravel5.3 news',
          'content' => 'the framework for php oop.'
        ]);
        $post->save();

        $post = new App\Post([
          'title' => 'Laravel5.7 news',
          'content' => 'the lates framework for php oop.'
        ]);
        $post->save();
    }
}
