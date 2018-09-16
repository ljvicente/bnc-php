<?php

namespace ljvicente\BNC;

/**
 * Api helper functions.
 *
 * @author Leo <jemnuineuron@gmail.com>
 */
trait QueryTrait
{
    /**
     * Set custom filters.
     *
     * @param array $filter_array
     * @return Company
     */
    public function filter($filter_array)
    {
        $filters = self::DEFAULT_FILTERS;
        
        foreach ($filter_array as $filter_key => $filter_value) {
            $filters[$filter_key] = $filter_value;
        }
        $this->filters = $filters;

        return $this;
    }

    /**
     * Build query without http encoding.
     *
     * @param array $filters
     * @return string
     */
    private function buildQueryString($filters)
    {
        $query = '';

        foreach ($filters as $filter_key => $filter_value) {
            $query .= "{$filter_key}={$filter_value}&";
        }

        return $query;
    }
}
