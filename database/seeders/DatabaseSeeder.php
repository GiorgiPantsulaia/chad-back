<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Genre;
use App\Models\Quote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Comment::factory(10)->create();
        $genres=[
            [
                'title'=>[
                    'en'=>'Adventure',
                    'ka'=>'სათავგადასავლო'
            ],
        ],
            [
                'title'=>[
                        'en'=>'Action',
                        'ka'=>'ექშენი'
                        ]
            ],
            [
                'title'=>[
                    'en'=>'Detective',
                    'ka'=>'დეტექტივი'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Drama',
                    'ka'=>'დრამა'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Western',
                    'ka'=>'ვესტერნი'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Comedy',
                    'ka'=>'კომედია'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Melodrama',
                    'ka'=>'მელოდრამა'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Fantasy',
                    'ka'=>'ზღაპრული(ფენტეზი)'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Horror',
                    'ka'=>'საშინელებათა ფილმი'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Sport',
                    'ka'=>'სპორტული'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Anime',
                    'ka'=>'ანიმე'
                        ]
                ],
            [
                'title'=>[
                    'en'=>'Auto',
                    'ka'=>'ავტო'
                    ]
                ],
                    
         
        ];
        foreach ($genres as $genre) {
            // dd($genre);
            Genre::create($genre);
        }
    }
}
