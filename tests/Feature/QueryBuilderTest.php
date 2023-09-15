<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
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

    public function insertManyCategories()
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table("categories")->insert([
                "id" => "CATEGORY - $i",
                "name" => "Category $i",
                // "created_at" => "2020-10-10 10:10:10"
            ]);
        }
    }

    // CHUNK
    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table('categories')
            ->orderBy("created_at")
            ->chunk(10, function ($categories) {
                self::assertNotNull($categories);
                self::assertCount(10, $categories);
                Log::info("Start Chunk");
                foreach ($categories as $item) {
                    Log::info(json_encode($item));
                }
                Log::info("End Chunk");
            });
    }

    // LAZY
    public function testLazy()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("created_at")->lazy(10);
        // $collection = DB::table("categories")->orderBy("created_at")->lazy(10)->take(3); // mengambil 3 hasil pertama dari query yang pertama (query dilakukan sekali)
        // $collection = DB::table("categories")->orderBy("created_at")->lazy()->take(10); // mengambil 10 hasil dari query dengan limit 1000 
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // CURSOR
    public function testCursor()
    {
        $this->insertManyCategories();

        $collection = DB::table("categories")->orderBy("created_at")->cursor();
        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    // QUERY BUILDER AGGREGATE
    public function testQueryBuilderAggregate()
    {
        $this->insertProducts();

        $result = DB::table("products")->count("id");
        self::assertEquals(2, $result);

        $result = DB::table("products")->min("price");
        self::assertEquals(18000000, $result);

        $result = DB::table("products")->max("price");
        self::assertEquals(20000000, $result);

        $result = DB::table("products")->avg("price");
        self::assertEquals(19000000, $result);

        $result = DB::table("products")->sum("price");
        self::assertEquals(38000000, $result);
    }

    // QUERY BUILDER RAW AGGREGATE
    public function testQueryBuilderRawAggregate()
    {
        $this->insertProducts();

        $collection = DB::table('products')->select([
            DB::raw("count(id) as id"),
            DB::raw("min(price) as min_price"),
            DB::raw("max(price) as max_price")
        ])->get();

        self::assertNotNull($collection);
        self::assertEquals(2, $collection[0]->id);
        self::assertEquals(18000000, $collection[0]->min_price);
        self::assertEquals(20000000, $collection[0]->max_price);
    }

    // QUERY BUILDER GROUPING
    public function insertProductsFood()
    {
        DB::table("products")->insert([
            "id" => "3",
            "name" => "Mie Ayam",
            "price" => 20000,
            "category_id" => "FOOD"
        ]);

        DB::table("products")->insert([
            "id" => "4",
            "name" => "Bakso",
            "price" => 20000,
            "category_id" => "FOOD"
        ]);
    }

    public function testGroupBy()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"), DB::raw("sum(price) as total_price"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->get();

        self::assertNotNull($collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item, JSON_PRETTY_PRINT));
        });
    }

    public function testGroupByHaving()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"), DB::raw("sum(price) as total_price"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->having(DB::raw("sum(price)"), ">", 40000)
            ->get();

        self::assertNotNull($collection);
        self::assertEquals("SMARTPHONE", $collection[0]->category_id);
        self::assertEquals("38000000", $collection[0]->total_price);
        self::assertEquals("2", $collection[0]->total_product);

        $collection->each(function ($item) {
            Log::info(json_encode($item, JSON_PRETTY_PRINT));
        });
    }

    // QUERY BUILDER LOCKING
    public function testQueryBuilderLocking()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->where("id", "=", '1')
            ->lockForUpdate()
            ->get();

        self::assertNotNull($collection);

        $collection->each(function ($item) {
            Log::info(json_encode($item, JSON_PRETTY_PRINT));
        });
    }

    // PAGINATION
    public function testPagination()
    {
        $this->insertManyCategories();

        $pagination = DB::table("categories")
            ->paginate(perPage: "10", columns: "*", page: 4);

        foreach ($pagination as $page) {
            Log::info(json_encode($page));
        }

        self::assertEquals(4, $pagination->currentPage());
        self::assertEquals(10, $pagination->perPage()); // dalam 1 halaman ada 10 record
        self::assertEquals(10, $pagination->lastPage()); // di halaman terakhir ada 10 record
        self::assertEquals(100, $pagination->total()); // total record dai semua halaman 
        self::assertEquals(10, count($pagination->items())); // total halaman dari categories jika di bagi 10
    }

    public function testIterateAllPagination()
    {
        $this->insertManyCategories();

        $pages = 1;
        while (true) {
            $pagination = DB::table("categories")
                ->orderBy("created_at", "asc")
                ->paginate(perPage: "10", columns: "*", page: $pages);

            self::assertNotNull($pagination);
            if ($pagination->isEmpty()) {
                break;
            } else {
                $pages++;
                foreach ($pagination as $page) {
                    Log::info(json_encode($page));
                }
            }
        }
    }
}
