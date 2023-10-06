<?php

namespace Opengeek;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The source data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * @param array $items Pre-populate collection with this key-value array
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    /**
     * Set collection item
     *
     * @param mixed $key   The data key
     * @param mixed $value The data value
     */
    public function set(mixed $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get collection item for key
     *
     * @param mixed $key     The data key
     * @param mixed $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * Add item to collection, replacing existing items with the same data key
     *
     * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get all items in collection
     *
     * @return array The collection's source data
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Get collection keys
     *
     * @return array The collection's source data keys
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Does this collection have a given key?
     *
     * @param mixed $key The data key
     *
     * @return bool
     */
    public function has(mixed $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove item from collection
     *
     * @param mixed $key The data key
     */
    public function remove(mixed $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Remove all items from collection
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Does this collection have a given key?
     *
     * @param  mixed $offset The data key
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get collection item for key
     *
     * @param mixed $offset The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set collection item
     *
     * @param mixed $offset   The data key
     * @param mixed $value The data value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Remove item from collection
     *
     * @param mixed $offset The data key
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * Get number of items in collection
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get collection iterator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }
}
