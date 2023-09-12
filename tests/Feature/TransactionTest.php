<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }

    public function testTransactioSuccess()
    {
        DB::transaction(function () {
            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "GADGET", "Gadget", "Gadget Categories", "2020-10-10 10:10:10"
            ]);

            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "FOOD", "Food", "Food Categories", "2020-10-10 10:10:10"
            ]);
        });

        $results = DB::select("select * from categories");
        self::assertCount(2, $results);
    }

    public function testTransactioFailed()
    {
        try {
            DB::transaction(function () {
                DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                    "GADGET", "Gadget", "Gadget Categories", "2020-10-10 10:10:10"
                ]);

                DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                    "GADGET", "Food", "Food Categories", "2020-10-10 10:10:10"
                ]);
            });
        } catch (QueryException $error) {
            Log::error($error->getMessage());
        }

        $results = DB::select("select * from categories");
        self::assertCount(0, $results);
    }

    public function testTransactionManualSuccess()
    {
        try {
            DB::beginTransaction();

            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "GADGET", "Gadget", "Gadget Categories", "2020-10-10 10:10:10"
            ]);

            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "FOOD", "Food", "Food Categories", "2020-10-10 10:10:10"
            ]);

            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
            Log::error($error->getMessage());
        }

        $results = DB::select("select * from categories");
        self::assertCount(2, $results);
    }

    public function testTransactionManualFailed()
    {
        try {
            DB::beginTransaction();

            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "GADGET", "Gadget", "Gadget Categories", "2020-10-10 10:10:10"
            ]);

            DB::insert("insert into categories (id, name, description, created_at) values (?, ?, ?, ?)", [
                "GADGET", "Food", "Food Categories", "2020-10-10 10:10:10"
            ]);

            DB::commit();
        } catch (QueryException $error) {
            DB::rollBack();
            Log::error($error->getMessage());
        }

        $results = DB::select("select * from categories");
        self::assertCount(0, $results);
    }
}
