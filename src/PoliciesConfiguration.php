<?php

namespace Twomedia\PoliciesBuilder;

use ArrayAccess;

class PoliciesConfiguration implements ArrayAccess
{
    protected array $config = [
        'brand' => '2media',
        'variant' => 'default',
    ];

    public static function make(): self
    {
        return new self();
    }

    public function domain(string $domain): self
    {
        $this->config['domain'] = $domain;

        return $this;
    }

    public function brand(string $brandName): self
    {
        $this->config['brand'] = $brandName;

        return $this;
    }

    public function variant(string $variant): self
    {
        $this->config['variant'] = $variant;

        return $this;
    }

    public function languages(array $languages): self
    {
        $this->config['languages'] = $languages;

        return $this;
    }

    public function types(array $types): self
    {
        $this->config['types'] = $types;

        return $this;
    }

    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->config[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->config[] = $value;
        } else {
            $this->config[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->config[$key]);
    }
}
