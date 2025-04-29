<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->text(200),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => 'https://source.unsplash.com/random/800x600/?themepark,freizeitpark',
            'category_id' => BlogCategory::inRandomOrder()->first()?->id ?? BlogCategory::factory(),
            'coupon_code' => $this->faker->optional()->bothify('COUPON-###'),
            'valid_until' => $this->faker->optional()->dateTimeBetween('+1 days', '+3 months'),
            'publish_start' => now()->subDays(rand(0, 30)),
            'publish_end' => now()->addDays(rand(30, 90)),
            'seo_title' => $title,
            'seo_description' => $this->faker->text(160),
            'status' => $this->faker->randomElement(['draft', 'published', 'scheduled']),
        ];
    }
}
