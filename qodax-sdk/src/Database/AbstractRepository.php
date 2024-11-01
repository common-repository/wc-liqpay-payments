<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Database;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\Exceptions\Database\QueryException;
class AbstractRepository
{
    protected bool $isStrictMode = \true;
    protected \wpdb $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    /**
     * @throws QueryException
     */
    protected function insert(string $table, array $values, array $casts = []) : int
    {
        $preparedValues = [];
        $format = [];
        foreach ($values as $key => $value) {
            $format[$key] = $this->getValueFormat($value);
            $preparedValues[$key] = $this->prepareValue($value);
        }
        $result = $this->wpdb->insert($table, $preparedValues, $format);
        if ($result === \false && $this->isStrictMode) {
            throw new QueryException('SQL insert query failed');
        }
        return $this->wpdb->insert_id;
    }
    /**
     * @throws QueryException
     */
    protected function update(string $table, array $values, array $where, array $casts = []) : int
    {
        $preparedValues = [];
        $format = [];
        foreach ($values as $key => $value) {
            $format[$key] = $this->getValueFormat($value);
            $preparedValues[$key] = $this->prepareValue($value);
        }
        $result = $this->wpdb->update($table, $preparedValues, $where, $format);
        if ($result === \false && $this->isStrictMode) {
            throw new QueryException('SQL update query failed');
        }
        return (int) $result;
    }
    /**
     * @param mixed $value
     * @return string
     */
    protected function getValueFormat($value) : string
    {
        if (\is_int($value)) {
            return '%d';
        } elseif (\is_float($value)) {
            return '%f';
        }
        return '%s';
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function prepareValue($value)
    {
        return \is_array($value) ? wp_json_encode($value) : $value;
    }
}
