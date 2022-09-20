<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\SplFileInfo;


class Post
{
    public $title;

    public $excerpt;

    public $date;

    public $body;

    public $slug;

    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }


    public static function all(): Collection
    {
        return cache()->rememberForever('posts.all', function () {

            return collect(File::files(resource_path("posts")))
                ->map(function (SplFileInfo $file) {
                    $document = YamlFrontMatter::parseFile($file->getPathname());
                    return new Post(
                        $document->title,
                        $document->excerpt,
                        $document->date,
                        $document->body(),
                        $document->slug
                    );
                });
//        ->sortByDesc('date');
        });


    }

    public static function find($slug): mixed
    {
        return static::all()->firstWhere('slug', $slug);
    }

    public static function findOrFail($slug): mixed
    {
        $post = static::find($slug);

        if(! $post){
            throw new ModelNotFoundException();
        }

        return $post;
    }
}
