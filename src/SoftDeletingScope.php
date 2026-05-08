<?php

declare(strict_types=1);
/**
 * This file is part of richard8768/hyperf-softdelete.
 *
 * @link     https://github.com/richard8768/hyperf-softdelete
 * @contact  444626008@qq.com
 * @license  https://github.com/richard8768/hyperf-softdelete/blob/main/LICENSE
 */

namespace Richard\HyperfSoftdelete;

use Hyperf\Database\Model\SoftDeletingScope as HyperfSoftDeletingScope;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;

class SoftDeletingScope  extends HyperfSoftDeletingScope
{
    /**
     * Apply the scope to a given Model query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getQualifiedDeletedAtColumn(), $model->getUnDeletedValue());
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->freshTimestamp()->timestamp,
            ]);
        });
    }

    /**
     * Add the restore extension to the builder.
     */
    protected function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getDeletedAtColumn() => $builder->getModel()->getUnDeletedValue()]);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     */
    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(), $model->getUnDeletedValue()
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     */
    protected function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedDeletedAtColumn(), '>', $model->getUnDeletedValue());

            return $builder;
        });
    }
}
