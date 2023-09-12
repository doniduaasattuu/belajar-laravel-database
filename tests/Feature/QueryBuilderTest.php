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
}
