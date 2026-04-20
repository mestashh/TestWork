<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function createCategories(): Collection
    {
        return collect([
            Category::create([
                'name' => 'Toys',
                'description' => fake()->text(255),
            ]),
            Category::create([
                'name' => 'Electronics',
                'description' => fake()->text(255),
            ]),
            Category::create([
                'name' => 'Food',
                'description' => fake()->text(255),
            ]),
        ]);
    }

    #[Test]
    public function guest_can_see_all_categories(): void
    {
        $this->createCategories();

        $request = $this->getJson('api/categories');

        $request->assertStatus(200);

        $request->assertJsonCount(3, 'data');
    }

    #[Test]
    public function guest_cannot_create_category(): void
    {
        $this->createCategories();

        $request = $this->postJson('api/categories', [
            'name' => 'test',
            'description' => 'test',
            'price' => 100,
            'category_id' => 1,
        ]);

        $request->assertStatus(405);
    }

    #[Test]
    public function guest_cannot_update_category(): void
    {
        $categories = $this->createCategories();

        $request = $this->putJson("api/categories/{$categories->first()}", [
            'name' => 'test',
            'description' => 'test',
        ]);

        $request->assertStatus(404);
    }

    #[Test]
    public function guest_cannot_delete_category(): void
    {
        $categories = $this->createCategories();

        $request = $this->deleteJson("api/categories/{$categories->first()}");

        $request->assertStatus(404);
    }
}
