<?php

namespace Laravel\Scout\Tests\Unit;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\SearchableScope;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class SearchableScopeTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_chunks_by_id()
    {
        $builder = m::mock(Builder::class);
        $builder->shouldReceive('macro')->with('searchable', m::on(function ($callback) use ($builder) {
            $model = m::mock(stdClass::class);
            $builder->shouldReceive(['getModel' => $model]);
            $builder->shouldReceive(['qualifyColumn' => 'stub.id']);
            $model->shouldReceive(['getScoutKeyName' => 'id']);
            $model->shouldReceive(['getKeyName' => 'id']);
            $builder->shouldReceive('chunkById')->with(500, m::type(\Closure::class), 'stub.id', 'id');
            $callback($builder, 500);

            return true;
        }));
        $builder->shouldReceive('macro')->with('unsearchable', m::on(function ($callback) use ($builder) {
            $builder->shouldReceive('chunkById')->with(500, m::type(\Closure::class));
            $callback($builder, 500);

            return true;
        }));

        (new SearchableScope())->extend($builder);
    }
}
