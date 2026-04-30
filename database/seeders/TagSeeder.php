<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React',
            'Node.js', 'Python', 'Django', 'MySQL', 'PostgreSQL',
            'MongoDB', 'Redis', 'Docker', 'AWS', 'Git',
            'REST API', 'GraphQL', 'Tailwind CSS', 'Bootstrap',
            'TypeScript', 'Java', 'Spring Boot', 'C#', '.NET',
            'Flutter', 'React Native', 'Swift', 'Kotlin',
            'DevOps', 'CI/CD', 'Linux', 'Nginx',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        }
    }
}
