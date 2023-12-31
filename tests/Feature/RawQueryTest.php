<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
    }

    public function testCrud()
    {
        DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
            "GADGET", "Gadget", "Gadget Categories", "2020-10-10 10:10:10"
        ]);

        $results = DB::select("select * from categories");
        self::assertCount(1, $results);
        self::assertEquals("GADGET", $results[0]->id);
        self::assertEquals("Gadget", $results[0]->name);
        self::assertEquals("Gadget Categories", $results[0]->description);
        self::assertEquals("2020-10-10 10:10:10", $results[0]->created_at);
    }

    public function testNamedCrud()
    {
        DB::insert("insert into categories (id, name, description, created_at) values (:id, :name, :description, :created_at)", [
            "id" => "GADGET",
            "name" => "Gadget",
            "description" => "Gadget Categories",
            "created_at" => "2020-10-10 10:10:10"
        ]);

        $results = DB::select("select * from categories");
        self::assertCount(1, $results);
        self::assertEquals("GADGET", $results[0]->id);
        self::assertEquals("Gadget", $results[0]->name);
        self::assertEquals("Gadget Categories", $results[0]->description);
        self::assertEquals("2020-10-10 10:10:10", $results[0]->created_at);
    }
}
