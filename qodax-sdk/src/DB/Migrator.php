<?php

declare (strict_types=1);
namespace Qodax\WcLiqPayPayments\Vendor\QodaxSdk\DB;

use Qodax\WcLiqPayPayments\Vendor\QodaxSdk\DB\Migration;
class Migrator
{
    private string $historyOptionName;
    private \wpdb $db;
    /**
     * @var Migration[]
     */
    private array $migrations = [];
    /**
     * @var array
     */
    private array $history = [];
    /**
     * @var int
     */
    private int $works = 0;
    public function __construct(string $historyOptionName)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->historyOptionName = $historyOptionName;
        $this->collate = $wpdb->get_charset_collate();
        $history = get_option($this->historyOptionName);
        if ($history) {
            $this->history = \json_decode($history, \true);
        } else {
            $this->history = [];
        }
    }
    public function __get($name)
    {
        return $this->{$name};
    }
    /**
     * @param Migration $migration
     */
    public function addMigration(Migration $migration)
    {
        if (!isset($this->migrations[$migration->name()])) {
            $this->migrations[$migration->name()] = $migration;
        }
    }
    /**
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->migrations as $migration) {
            if (!\in_array($migration->name(), $this->history)) {
                $migration->up($this->db);
                if (!$this->db->last_error) {
                    $this->history[] = $migration->name();
                    $this->works++;
                }
            }
        }
        if ($this->works) {
            update_option($this->historyOptionName, wp_json_encode($this->history));
        }
    }
}
