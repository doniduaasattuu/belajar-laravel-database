<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
    }

    // QUERY BUILDER INSERT
    public function testQueryBuilderInsert()
    {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget"
        ]);

        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food"
        ]);

        $result = DB::select("select * from categories");
        self::assertCount(2, $result);

        $result = DB::select("select count(id) as total from categories");
        self::assertEquals(2, $result[0]->total);
    }

    // QUERY BUILDER SELECT
    public function testQueryBuilderSelect()
    {
        $this->testQueryBuilderInsert();

        $collection = DB::table("categories")->select("id", "name")->get();
        self::assertNotNull($collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });

        $collection = DB::table("categories")->select()->get();
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // QUERY BUILDER WHERE
    public function insertCategories()
    {
        DB::table("categories")->insert([
            "id" => "SMARTPHONE",
            "name" => "Smartphone",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "LAPTOP",
            "name" => "Laptop",
            "created_at" => "2020-10-10 10:10:10"
        ]);
        DB::table("categories")->insert([
            "id" => "FASHION",
            "name" => "Fashion",
            "created_at" => "2020-10-10 10:10:10"
        ]);
    }

    public function testQueryBuilderWhere()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function (Builder $builder) {
            $builder->where("id", "=", "SMARTPHONE");
            $builder->orWhere("id", "=", "LAPTOP");
            // SELECT * FROM categories WHERE (id = smartphone OR id = laptop);
        })->get();

        self::assertCount(2, $collection);
    }

    public function testQueryBuilderWhereBetween()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereBetween("created_at", [
            "2020-10-09 10:10:10", "2020-10-11 10:10:10"
        ])->get();

        self::assertCount(4, $collection);

        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testQueryBuilderWhereNotBetween()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNotBetween("created_at", [
            "2020-10-09 10:10:10", "2020-10-11 10:10:10"
        ])->get();

        self::assertCount(0, $collection);
    }

    public function testQueryBuilderWhereIn()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereIn("id", ["SMARTPHONE", "FASHION"])->get();

        self::assertCount(2, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testQueryBuilderWhereNotIn()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNotIn("id", ["LAPTOP", "SMARTPHONE", "FASHION"])->get();

        self::assertCount(1, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
            // FOOD
        }
    }

    public function testQueryBuilderWhereNull()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNull("description")->get();
        self::assertCount(4, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testQueryBuilderWhereNotNull()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereNotNull("description")->get();
        self::assertCount(0, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    public function testWhereDateMethod()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->whereDate("created_at", "2020-10-10")->get();
        self::assertCount(4, $collection);

        $collection = DB::table("categories")->whereMonth("created_at", "10")->get();
        self::assertCount(4, $collection);

        $collection = DB::table("categories")->whereYear("created_at", "2020")->get();
        self::assertCount(4, $collection);
    }

    // QUERY BUILDER UPDATE
    public function testUpdate()
    {
        $this->insertCategories();

        DB::table('categories')->where("id", "=", "SMARTPHONE")->update([
            "name" => "Handphone"
        ]);

        $result = DB::table("categories")->where("name", "=", "Handphone")->get();
        self::assertEquals($result[0]->name, "Handphone");

        $result = DB::table("categories")->where("name", "=", "Smartphone")->get();
        // self::assertNotNull($result);
        var_dump($result);
    }

    public function testUpdateOrInsert()
    {
        DB::table('categories')->updateOrInsert(
            [
                "id" => "VOUCHER"
            ],
            [
                "name" => "Voucher",
                "description" => "Ticket and Voucher",
                "created_at" => "2020-10-10 10:10:10"
            ]
        );

        $result = DB::table('categories')->where("id", "=", "VOUCHER")->get();
        self::assertNotNull($result);
        self::assertEquals("Voucher", $result[0]->name);

        foreach ($result as $item) {
            Log::info(json_encode($item));
        }
    }

    // INCREMENT
    public function testIncrement()
    {
        DB::table("counters")->increment("counter", 1);

        $result = DB::select('select * from counters');
        self::assertNotNull($result);
        foreach ($result as $item) {
            Log::info(json_encode($item));
        }
    }

    // DECREMENT
    public function testDecrement()
    {
        DB::table("counters")->where("id", "=", "sample")->decrement("counter", 1);

        $result = DB::select('select * from counters');
        self::assertNotNull($result);
        foreach ($result as $item) {
            Log::info(json_encode($item));
        }
    }

    // QUERY BUILDER DELETE
    public function testDelete()
    {
        $this->insertCategories();

        DB::table("categories")->whereNotNull("id")->delete("SMARTPHONE");

        $result = DB::table("categories")->where("id", "=", "SMARTPHONE")->get();
        self::assertCount(0, $result);
    }

    // QUERY BUILDER TRUNCATE
    public function testTruncate()
    {
        $this->insertCategories();
        $result = DB::table("categories")->get();
        self::assertCount(4, $result);

        // DB::table('categories')->truncate();
        // $result = DB::table("categories")->get();
        // self::assertCount(0, $result);
    }

    // QUERY BUILDER JOIN
    public function insertProducts()
    {
        $this->insertCategories();

        DB::table("products")->insert([
            "id" => "1",
            "name" => "iPhone 14 Pro Max",
            "price" => 20000000,
            "category_id" => "SMARTPHONE"
        ]);

        DB::table("products")->insert([
            "id" => "2",
            "name" => "Samsung Galaxy S21 Ultra",
            "price" => 18000000,
            "category_id" => "SMARTPHONE"
        ]);
    }

    public function testQueryQuilderJoin()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->join("categories", "categories.id", "=", "products.category_id")
            ->select("products.id", "products.name", "products.price", "categories.name as category_name")
            ->get();

        self::assertNotNull($collection);

        foreach ($collection as $item) {
            Log::info(json_encode($item, JSON_PRETTY_PRINT));
        }
    }

    // QUERY BUILDER ORDERING
    public function testQueryQuilderOrdering()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->whereNotNull("id")
            ->orderBy("price", "desc")
            ->orderBy("name")
            ->get();

        self::assertNotNull($collection);
        self::assertCount(2, $collection);

        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    // QUERY BUILDER PAGING
    public function testQueryBuilderPaging()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->skip(0)
            ->take(2)
            ->get();

        self::assertNotNull($collection);
        self::assertCount(2, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }
}
