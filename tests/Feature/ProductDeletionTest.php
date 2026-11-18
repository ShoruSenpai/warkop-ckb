<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductDeletionTest extends TestCase
{
    private const DEPENDENCY_TABLES = [
        "product_recipes",
        "product_packagings",
        "product_option_groups",
        "product_discounts",
        "supplier_purchase_items",
        "order_items",
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestampsTz();
        });

        foreach (self::DEPENDENCY_TABLES as $dependencyTable) {
            Schema::create($dependencyTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId("product_id");
            });
        }
    }

    protected function tearDown(): void
    {
        foreach (array_reverse(self::DEPENDENCY_TABLES) as $dependencyTable) {
            Schema::dropIfExists($dependencyTable);
        }

        Schema::dropIfExists("products");

        parent::tearDown();
    }

    public function test_admin_can_delete_a_product_without_dependencies(): void
    {
        $this->authenticateAs("admin");
        $productId = $this->createProduct();

        $this->deleteJson("/api/products/{$productId}")
            ->assertOk()
            ->assertJsonPath("success", true);

        $this->assertDatabaseMissing("products", ["id" => $productId]);
    }

    public function test_deleting_a_missing_product_returns_not_found(): void
    {
        $this->authenticateAs("admin");

        $this->deleteJson("/api/products/999999")->assertNotFound();
    }

    #[DataProvider("productDependencies")]
    public function test_product_with_any_protected_dependency_cannot_be_deleted(
        string $dependencyTable,
    ): void {
        $this->authenticateAs("admin");
        $productId = $this->createProduct();
        DB::table($dependencyTable)->insert(["product_id" => $productId]);

        $this->deleteJson("/api/products/{$productId}")
            ->assertStatus(409)
            ->assertJsonPath("success", false);

        $this->assertDatabaseHas("products", ["id" => $productId]);
    }

    public static function productDependencies(): array
    {
        return array_combine(
            self::DEPENDENCY_TABLES,
            array_map(fn(string $table) => [$table], self::DEPENDENCY_TABLES),
        );
    }

    public function test_unauthenticated_user_cannot_delete_a_product(): void
    {
        $productId = $this->createProduct();

        $this->deleteJson("/api/products/{$productId}")->assertUnauthorized();
    }

    public function test_user_without_product_api_access_cannot_delete_a_product(): void
    {
        $this->authenticateAs("employee");
        $productId = $this->createProduct();

        $this->deleteJson("/api/products/{$productId}")->assertForbidden();
    }

    private function authenticateAs(string $role): void
    {
        Sanctum::actingAs(new User(["username" => "test", "role" => $role]));
    }

    private function createProduct(): int
    {
        return DB::table("products")->insertGetId([
            "name" => "Produk Uji",
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
