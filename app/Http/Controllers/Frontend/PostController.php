<?php

namespace Kommercio\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Kommercio\Facades\FrontendHelper;
use Kommercio\Models\CMS\Post;
use Kommercio\Http\Controllers\Controller;
use Kommercio\Facades\ProjectHelper;
use Kommercio\Models\CMS\PostCategory;

class PostController extends Controller
{
    public function viewPost($id)
    {
        $post = Post::findOrFail($id);

        $view_name = ProjectHelper::findViewTemplate($post->getViewSuggestions());

        return view($view_name, [
            'post' => $post,
            'seoModel' => $post
        ]);
    }

    public function viewCategory(Request $request, $id)
    {
        $postCategory = PostCategory::findOrFail($id);

        $qb = $postCategory->posts()->active();

        $posts = $qb->paginate(ProjectHelper::getConfig('post_options.limit'));
        $posts->setPath(FrontendHelper::getUrl($request->path()));

        $view_name = ProjectHelper::findViewTemplate($postCategory->getViewSuggestions());

        return view($view_name, [
            'postCategory' => $postCategory,
            'posts' => $posts,
            'seoModel' => $postCategory
        ]);
    }
}
