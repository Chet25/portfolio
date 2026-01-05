<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditorUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('blog-images', 'public');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => asset('storage/' . $path),
            ]
        ]);
    }

    public function fetchLink(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        try {
            // Use Laravel's HTTP client for safety and better performance
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url);

            if ($response->failed()) {
                return response()->json(['success' => 0]);
            }

            $html = $response->body();

            if (!$html) {
                return response()->json(['success' => 0]);
            }

            $doc = new \DOMDocument();
            @$doc->loadHTML($html);

            $title = '';
            $description = '';
            $image = '';

            // Get title
            $titleTags = $doc->getElementsByTagName('title');
            if ($titleTags->length > 0) {
                $title = $titleTags->item(0)->textContent;
            }

            // Get meta tags
            $metas = $doc->getElementsByTagName('meta');
            foreach ($metas as $meta) {
                $property = $meta->getAttribute('property');
                $name = $meta->getAttribute('name');
                $content = $meta->getAttribute('content');

                if ($property === 'og:title' || $name === 'title') {
                    $title = $content ?: $title;
                }
                if ($property === 'og:description' || $name === 'description') {
                    $description = $content;
                }
                if ($property === 'og:image') {
                    $image = $content;
                }
            }

            return response()->json([
                'success' => 1,
                'link' => $url,
                'meta' => [
                    'title' => $title,
                    'description' => $description,
                    'image' => [
                        'url' => $image,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => 0]);
        }
    }
}
