<?php

class ModelGenerator
{

    private $dns;

    private $db;

    private $table;

    private $tableColumns;

    private $outPath;

    private $out;

    function __construct($db, $outPath = null)
    {
        $this->tableColumns = [];
        
        // Verify if db is an istance of PDO class
        if ($db instanceof PDO) {
            // get dns from PDO instance
            $this->dns = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            $this->db = $db;
            $this->outPath = isset($outPath) ? $outPath : "";
        } else {
            return "I need a PDO instance to operate.";
        }
    }

    
    function createModelFromTableOrView($table)
    {
        $this->table = $table;
        
        // Under the dsn recovered, Recovery its data definition
        switch ($this->dns) {
            case 'db2':
                $this->getTableDefinitionDb2($this->db);
                break;
            case 'mssql':
                $this->getTableDefinitionMssql($this->db);
                break;
            case 'mysql':
                $this->getTableDefinitionMysql($this->db);
                break;
            case 'pgsql':
                $this->getTableDefinitionPgsql($this->db);
                break;
            case 'sqlite':
                $this->getTableDefinitionSqlite($this->db);
                break;
            case 'oracle':
                $this->getTableDefinitionOracle($this->db);
                break;
        }
        
        $this->out = "";
        $this->out .= $this->getHeader();
        $this->out .= $this->getParams();
        $this->out .= $this->getGetters();
        $this->out .= $this->getSetters();
        $this->out .= $this->getToString();
        $this->out .= $this->getFooter();
        
        file_put_contents($this->outPath . DIRECTORY_SEPARATOR . 'Model' . ucfirst($this->table) . ".php", $this->out);
        
        print_r("Model $table, successfully created!");
    }

    /**
     * Get data definition for DB2.
     */
    private function getTableDefinitionDb2($db)
    {
        return "I haven't in local a db server MS SQL, but I leave to you the possibility to implement this feature..\n";
    }

    /**
     * Get data definition for MS SQL.
     */
    private function getTableDefinitionMssql($db)
    {
        return "I haven't in local a db server MS SQL, but I leave to you the possibility to implement this feature..\n";
    }

    /**
     * Get data definition for MySQL.
     */
    private function getTableDefinitionMysql($db)
    {
        $sql = "SHOW COLUMNS FROM $this->table";
        $statement = $db->prepare($sql);
        
        if ($statement->execute()) {
            
            while ($row = $statement->fetch()) {
                $this->tableColumns[$row['Field']] = array(
                    'allow_null' => $row['Null'],
                    'decimal' => NULL,
                    'default' => $row['Default'],
                    'extra' => $row['Extra'],
                    'key' => $row['Key'],
                    'length' => NULL,
                    'name' => $row['Field'],
                    'text' => NULL,
                    'type' => $row['Type']
                );
            }
            ksort($this->tableColumns);
        } else {
            echo "problem";
            print_r($db->errorInfo());
        }
    }

    /**
     * Get data definition for PostgreSQL.
     */
    private function getTableDefinitionPgsql($db)
    {
        return "I haven't in local a db server PostgreSQL, but I leave to you the possibility to implement this feature..\n";
    }

    /**
     * Get data definition for SQLite.
     */
    private function getTableDefinitionSqlite($db)
    {
        return "I haven't in local a db server SQLite, but I leave to you the possibility to implement this feature..\n";
    }

    /**
     * Get data definition for Oracle.
     */
    private function getTableDefinitionOracle($db)
    {
        return "I haven't in local a db server Oracle, but I leave to you the possibility to implement this feature..\n";
    }
    
    private function getHeader()
    {
        return "<?php
/**
* Created automatically with PhpModelGenerator 1.0 ©2015 Ettore Moretti
*
*/
class Model_$this->table
{";
    }

    private function getParams()
    {
        $code = "";
        foreach ($this->tableColumns as $col)
            $code .= PHP_EOL . "        private \$" . $col['name'] . ";" . PHP_EOL;
        return $code;
    }

    private function getGetters()
    {
        $code = "";
        foreach ($this->tableColumns as $col) {
            $code .= PHP_EOL . PHP_EOL . "    /**
     *
     * @return the \$" . $col['name'] . "
     */
    public function get" . $col['name'] . "()
    {
        return \$this->" . $col['name'] . ";
    }";
        }
        return $code;
    }

    private function getSetters()
    {
        $code = "";
        foreach ($this->tableColumns as $col) {
            $code .= PHP_EOL . PHP_EOL . "    /**
     *
     * @param field_type \$" . $col['name'] . "
     */
    public function set" . $col['name'] . "(\$" . $col['name'] . ")
    {
        \$this->" . $col['name'] . "=\$" . $col['name'] . ";
    }";
        }
        return $code;
    }

    private function getToString()
    {
        $code = "";
        $code .= PHP_EOL . PHP_EOL . "    /**
     *
     * @return toString() of  Model_" . $this->table . "
     */
    public function toString()
    {
        return \"";
        
        foreach ($this->tableColumns as $col) {
            $code .= ucfirst($col['name']) . '=$this->' . $col['name'] . " ";
        }
        $code .= '";' . PHP_EOL . "    }";
        
        return $code;
    }

    private function getFooter()
    {
        return PHP_EOL . "}";
    }
}