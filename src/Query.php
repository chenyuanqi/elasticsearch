<?php

namespace chenyuanqi\elasticSearchService;

use App, Input;
use Illuminate\Pagination\LengthAwarePaginator;

class Query
{
    /**
     * The search index instance.
     *
     * @var \Mmanos\Search\Index
     */
    protected $index;

    /**
     * The raw query used by the current search index driver.
     *
     * @var mixed
     */
    protected $query;

    /**
     * The search conditions for the query.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * The columns that should be returned.
     *
     * @var array
     */
    protected $columns;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    protected $limit;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    protected $offset;

    /**
     * Any user defined callback functions to help manipulate the raw
     * query instance.
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * Flag to remember if callbacks have already been executed.
     * Prevents multiple executions.
     *
     * @var bool
     */
    protected $callbacks_executed = false;

    protected static $client;

    public function __construct($index)
    {
        $this->index = $index;
        if (!static::$client) {
            $host = Config::get('elasticsearch.'.$index.'.host', []);
            static::$client = ClientBuilder::create()->setHosts($host)->build();
        }
    }

    public function where($field, $value)
    {
        $this->query = $this->index->addConditionToQuery($this->query, [
            'field'    => $field,
            'value'    => $value,
            'required' => true,
            'filter'   => true,
        ]);

        return $this;
    }

    public function search($field, $value, array $options = [])
    {
        $this->query = $this->index->addConditionToQuery($this->query, [
            'field'      => $field,
            'value'      => $value,
            'required'   => array_get($options, 'required', true),
            'prohibited' => array_get($options, 'prohibited', false),
            'phrase'     => array_get($options, 'phrase', false),
            'fuzzy'      => array_get($options, 'fuzzy', null),
        ]);

        return $this;
    }

    public function addCallback($callback, $driver = null)
    {
        if (!empty($driver)) {
            if (is_array($driver)) {
                if (!in_array($this->index->driver, $driver)) {
                    return $this;
                }
            } else if ($driver != $this->index->driver) {
                return $this;
            }
        }

        $this->callbacks[] = $callback;

        return $this;
    }

    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->limit  = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function delete()
    {
        $this->columns = null;
        $results       = $this->get();

        foreach ($results as $result) {
            $this->index->delete(array_get($result, 'id'));
        }
    }

    public function paginate($num = 10)
    {
        $page = (int)Input::get('page', 1);

        $this->limit($num, ($page - 1) * $num);

        return new LengthAwarePaginator($this->get(), $this->count(), $num, $page);
    }

    public function count()
    {
        $this->executeCallbacks();

        return $this->index->runCount($this->query);
    }

    /**
     * Execute the current query and return the results.
     *
     * @return array
     */
    public function get()
    {
        $options = [];
        if ($this->columns) {
            $options['columns'] = $this->columns;
        }

        if ($this->limit) {
            $options['limit']  = $this->limit;
            $options['offset'] = $this->offset;
        }

        $this->executeCallbacks();

        $results = $this->index->runQuery($this->query, $options);

        if ($this->columns && !in_array('*', $this->columns)) {
            $new_results = [];
            foreach ($results as $result) {
                $new_result = [];
                foreach ($this->columns as $field) {
                    if (array_key_exists($field, $result)) {
                        $new_result[$field] = $result[$field];
                    }
                }
                $new_results[] = $new_result;
            }
            $results = $new_results;
        }

        return $results;
    }

    /**
     * Execute any callback functions. Only execute once.
     *
     * @return void
     */
    protected function executeCallbacks()
    {
        if ($this->callbacks_executed) {
            return;
        }

        $this->callbacks_executed = true;

        foreach ($this->callbacks as $callback) {
            if ($q = call_user_func($callback, $this->query)) {
                $this->query = $q;
            }
        }
    }
}