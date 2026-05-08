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

use Hyperf\Database\Model\SoftDeletes as HyperfSoftDeletes;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @method static \Hyperf\Database\Model\Builder<static> withTrashed(bool $withTrashed = true)
 * @method static \Hyperf\Database\Model\Builder<static> onlyTrashed()
 * @method static \Hyperf\Database\Model\Builder<static> withoutTrashed()
 * @method static static restoreOrCreate(array<string, mixed> $attributes = [], array<string, mixed> $values = [])
 * @method static static createOrRestore(array<string, mixed> $attributes = [], array<string, mixed> $values = [])
 */
trait SoftDeletes
{
    use HyperfSoftDeletes;

    /**
     * Boot the soft deleting trait for a model.
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope());
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return null|bool
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($event = $this->fireModelEvent('restoring')) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return false;
            }
        }

        $this->{$this->getDeletedAtColumn()} = $this->getUnDeletedValue();

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored');

        return $result;
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} != $this->getUnDeletedValue();
    }


    public function getUnDeletedValue()
    {
        return defined('static::UN_DELETED_VALUE') ? static::UN_DELETED_VALUE : 0;
    }

    /**
     * Perform the actual delete query on this model instance.
     */
    protected function runSoftDelete()
    {
        $query = $this->newModelQuery()->where($this->getKeyName(), $this->getKey());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $time->timestamp];

        $this->{$this->getDeletedAtColumn()} = $time->timestamp;

        if ($this->timestamps && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);
    }
}
