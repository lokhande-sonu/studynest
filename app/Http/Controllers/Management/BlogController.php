<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('b_id', 'desc')->get();
        return view('management.blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('management.blog.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'b_title' => 'required|string|max:255',
            'b_content' => 'required',
            'b_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'b_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'b_title', 'b_content', 'b_status', 
            'b_meta_title', 'b_meta_description', 'b_meta_keywords'
        ]);
        
        $data['b_slug'] = Str::slug($request->b_title);
        
        // Ensure slug is unique
        $originalSlug = $data['b_slug'];
        $count = 1;
        while (Blog::where('b_slug', $data['b_slug'])->exists()) {
            $data['b_slug'] = $originalSlug . '-' . $count++;
        }

        if ($request->hasFile('b_image')) {
            $uploadPath = 'uploads/blog-photos';
            if (!File::exists(public_path($uploadPath))) {
                File::makeDirectory(public_path($uploadPath), 0777, true);
            }
            $imageName = time() . '.' . $request->b_image->extension();
            $request->b_image->move(public_path($uploadPath), $imageName);
            $data['b_image'] = $imageName;
        }

        Blog::create($data);

        return redirect()->route('management.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('management.blog.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'b_title' => 'required|string|max:255',
            'b_content' => 'required',
            'b_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'b_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'b_title', 'b_content', 'b_status', 
            'b_meta_title', 'b_meta_description', 'b_meta_keywords'
        ]);

        if ($request->b_title != $blog->b_title) {
            $data['b_slug'] = Str::slug($request->b_title);
            $originalSlug = $data['b_slug'];
            $count = 1;
            while (Blog::where('b_slug', $data['b_slug'])->where('b_id', '!=', $id)->exists()) {
                $data['b_slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->hasFile('b_image')) {
            $uploadPath = 'uploads/blog-photos';
            
            // Delete old image
            if ($blog->b_image && File::exists(public_path($uploadPath . '/' . $blog->b_image))) {
                File::delete(public_path($uploadPath . '/' . $blog->b_image));
            }

            if (!File::exists(public_path($uploadPath))) {
                File::makeDirectory(public_path($uploadPath), 0777, true);
            }
            $imageName = time() . '.' . $request->b_image->extension();
            $request->b_image->move(public_path($uploadPath), $imageName);
            $data['b_image'] = $imageName;
        }

        $blog->update($data);

        return redirect()->route('management.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $uploadPath = 'uploads/blog-photos';
        
        if ($blog->b_image && File::exists(public_path($uploadPath . '/' . $blog->b_image))) {
            File::delete(public_path($uploadPath . '/' . $blog->b_image));
        }

        $blog->delete();

        return redirect()->route('management.blogs.index')->with('success', 'Blog deleted successfully.');
    }
    
    public function updateStatus(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->b_status = $blog->b_status == 1 ? 0 : 1;
        $blog->save();
        
        return redirect()->back()->with('success', 'Blog status updated successfully.');
    }
}
