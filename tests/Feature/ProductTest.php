<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function createProducts(): Collection
    {
        Category::create([
            'name' => 'Toys',
            'description' => fake()->text(255),
        ]);
        Category::create([
            'name' => 'Electronics',
            'description' => fake()->text(255),
        ]);
        Category::create([
            'name' => 'Food',
            'description' => fake()->text(255),
        ]);

        return Product::factory()->count(10)->create();
    }

    #[Test]
    public function guest_can_see_all_products(): void
    {

        $this->createProducts();
        $request = $this->getJson('api/products');

        $request->assertStatus(200);

        $request->assertJsonCount(10, 'data');
    }

    #[Test]
    public function guest_can_see_one_product(): void
    {

        $products = $this->createProducts();
        $request = $this->getJson("api/products/{$products->first()->id}");

        $request->assertStatus(200);
    }

    #[Test]
    public function guest_cannot_update_product(): void
    {

        $products = $this->createProducts();
        $request = $this->putJson("api/products/{$products->first()->id}", [
            'name' => 'Test',
        ]);

        $request->assertStatus(401);
    }

    #[Test]
    public function guest_cannot_create_product(): void
    {

        $products = $this->createProducts();
        $request = $this->postJson("api/products/{$products->first()->id}", [
            'name' => 'test',
            'description' => 'test',
            'price' => 100,
            'category_id' => 1,
        ]);

        $request->assertStatus(405);
    }

    #[Test]
    public function guest_cannot_delete_product(): void
    {

        $products = $this->createProducts();
        $request = $this->deleteJson("api/products/{$products->first()->id}");

        $request->assertStatus(401);
    }
}
