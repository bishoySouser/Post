<?php

namespace App\Http\Controllers;

use App\Post;
use App\Tag;
use App\Like;
use Auth;
use Gate;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function getIndex()
    {
      $post = Post::orderBy('created_at', 'desc')->paginate(2);
      return view('blog.index', ['posts'=> $post]);
    }
    public function getAdminIndex()
    {
      $post = Post::orderBy('title', 'desc')->get();
      return view('admin.index', ['posts'=> $post]);
    }
    public function getPost($id)
    {
      $post = Post::where('id',$id)->first();
      return view('blog.post', ['post'=> $post]);
    }
    public function getLikePost($id)
    {
      $post = Post::where('id', $id)->first();
      $like = new like();
      $post->likes()->save($like);
      return redirect()->back();
    }
    public function getAdminCreate()
    {
      $tags = Tag::all();
      return view('admin.create', ['tags'=> $tags]);
    }
    public function getAdminEdit($id)
    {
      $post = Post::find($id);
      $tags = Tag::all();
      return view('admin.edit', ['post'=> $post, 'postId'=> $id, 'tags'=> $tags]);
    }
    public function postAdminCreate(Request $request)
    {
      $this->validate($request, [
        'title' => 'required|min:10',
        'content' => 'required|min:20'
      ]);
      $user = Auth::user();
      if(!$user){
        return redirect()->back();
      }
      $post = new Post([
        'title' => $request->input('title'),
        'content' => $request->input('content')
      ]);
      $user->posts()->save($post);
      $post->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));
      return redirect()->route('admin.index')->with('info', 'Post created, Title is: '
      . $request->input('title'));
    }
    public function postAdminUpdate(Request $request)
    {
      $this->validate($request, [
        'title' => 'required|min:10',
        'content' => 'required|min:20'
      ]);
      $post = Post::find($request->input('id'));
      if (Gate::denies('update-post', $post)){
        return redirect()->back()->with('alert','You has not  premession for this post');
      }
      $post->title = $request->input('title');
      $post->content = $request->input('content');
      $post->save();
      // $post->tags()->attach($request->input('tags') === null ? [] : $request->input('tags'));
      $post->tags()->sync($request->input('tags') === null ? [] : $request->input('tags'));
       return redirect()->route('admin.index')->with('info', 'Post edited, new title is: '.
        $request->input('title'));
    }
    public function postAdminDelete($id)
    {
      $post = Post::find($id);
      if (Gate::denies('update-post', $post)){
        return redirect()->back();
      }
      $post->likes()->delete();
      $post->tags()->detach();
      $post->delete();
      return redirect()->route('admin.index')->with('info', 'Post deleted!');
    }
}
