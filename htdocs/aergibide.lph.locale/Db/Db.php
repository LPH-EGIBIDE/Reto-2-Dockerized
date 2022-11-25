<?php

namespace Db;

use PDOException;

class Db extends \PDO {


    // single instance of self shared among all instances
    private static ?Db $instance = null;
    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance(): ?Db
    {
        if (self::$instance != null) {
            return self::$instance;
        }
        return null;

    }

    public static function setInstance(string $host, string $user, string $password, string $database): void
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($host, $user, $password, $database);
        }
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __construct( string $host, string $user, string $password, string $database) {



        try {
            parent::__construct("mysql:host=$host;dbname=$database", $user, $password);
            $this->setAttribute(parent::ATTR_ERRMODE, parent::ERRMODE_EXCEPTION);
            $this->setAttribute(parent::ATTR_ERRMODE, parent::ERRMODE_SILENT);
            $this->query('SET NAMES utf8');
            $this->query('SET CHARACTER SET utf8');
        } catch(PDOException $e) {
            exit($e->getMessage());
            exit("ERROR GENERAL 1092");
        }

    }

    //TODO: Implementar metodos para agilizar el uso de la base de datos

    /*

    public function dbquery($query)
    {
        if($this->query($query))
        {
            return true;
        }

    }
    public function get_result($query)
    {
        $result = $this->query($query);
        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row;
        } else
            return null;


    }

    public function prepared_query(string $query, string $paramtypes, $params){
        try {
            $stmt = parent::prepare($query);

            if($paramtypes&&$params)
            {
                $bind_names[] = $paramtypes;
                for ($i=0; $i<count($params);$i++)
                {
                    $bind_name = 'bind' . $i;
                    $$bind_name = $params[$i];
                    $bind_names[] = &$$bind_name;
                }
                call_user_func_array(array($stmt,'bind_param'),$bind_names);
            }
            $stmt->execute();
            return $stmt;
        } catch (Exception $exception){
            return false;
        }
    }

    public function prepared_select(string $query, string $paramtypes, $params){
        try {
            $q = $this->prepared_query( $query,  $paramtypes, $params);
            return $q->get_result();
        } catch (Exception $exception){
            return false;
        }
    }

    public function get_row(string $query, string $paramtypes, $params){
        $q = $this->prepared_select( $query,  $paramtypes, $params);
        return $q->fetch_row();
    }
    */

}