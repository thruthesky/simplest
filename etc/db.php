<?php

include 'db-config.php';


$_mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

/*
 * This is the "official" OO way to do it,
 * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
 */
if ($_mysqli->connect_error) {
    $msg = 'Connect Error (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error;
    _error('db_connection', $msg);
}

_log("db connected: ", $_mysqli->host_info );


function _table($name) {
    return DB_PREFIX . $name;
}


class SimplestDB {


    /**
     * @var mysqli
     */
    private $db = null;

    private $table_name = '';
    private $fields = [];
    private $where = '';
    public function __construct()
    {
        global $_mysqli;
        $this->db = $_mysqli;

    }

    /**
     *
     * Returns the first element of the first row.
     *
     * @param $q
     * @return mixed
     *
     *  it returns false on ERROR.
     *  it returns
     *      null on no data
     *      or value
     *
     * @example
     *  $re = db()->result("SELECT * FROM " . _table('tests') . " WHERE name='jaeho'");
     *  $re ? testOk("Got idx: $idx") : testBad("Failed on result()");

     */
    public function result( $q )
    {
        $row = $this->row($q);
        if ( $row === false) return $row;            /// IF false returned, it is an ERROR.
        if ($row) {
            foreach ($row as $k => $v) {
                return $v;
            }
        }
        return null;
    }

        /**
     *
     *
     * This returns the first row of the query result set.
     * @desc The query should result in only one record or invoke with 'LIMIT 1'.
     *
     * @param $q
     *
     * @return mixed
     *  it returns false on ERROR.
     *  it returns empty array if there is no data.
     *
     * @example
     *  $row = db()->row("SELECT * FROM " . _table('tests') . " WHERE name='jaeho' LIMIT 1");
     *  count($row) ? testOk("row() ok. idx: $row[idx]") : testBad('row() failed');

     */
    public function row($q)
    {
        $re = $this->rows( $q );
        if ( $re === false) return $re;            /// IF false returned, it is an ERROR.
        else {
            if ( isset($re[0]) ) return $re[0];
            else return [];
        }
    }

    /**
     *
     * @return array|bool
     *          만약 결과가 없으면 빈 배열을 리턴한다.
     *          쿼리에 에러가 있으면 FALSE 를 리턴하고 debug.log 에 기록을 남긴다.
     *
     */

    function rows($q)
    {
        _log("DB::rows() query: " . $q);
        $rows = [];
        $result = $this->db->query($q);
        if ( $no = $this->db->errno ) {
            _log("DB::rows() error no: $no, message: " . $this->db->error);
            return FALSE;
        }
        if ( $result ) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            $result->close();
        }
        return $rows;
    }

    public function table($table_name) {
        $this->table_name = DB_PREFIX . $table_name;
        return $this;
    }
    public function record($fields) {
        $this->fields = $fields;
        return $this;
    }

    public function where($cond) {
        $this->where = $cond;
        return $this;
    }


    /**
     * @param $keys_and_values
     *              If it is not given, it uses $this->fields which is set by db()->table()->record();
     * @return bool|int
     *  on success - last insert id will be returned as number.
     *  on failure - FALSE will be return and error message is logged on debug log.
     *
     * @example
     *      $re = db()->table('tests')->record(['name' => 'jaeho'])->insert();
     *      $re ? testOk("insert ok. insert id: $re") : testBad('insert failed');
     *
     * @exmaple with insert() data
     *      db()->table('tests')->insert(['name' => 'jiyeon']);
     */
    public function insert($keys_and_values = []) {
        if ( empty($keys_and_values ) ) $keys_and_values = $this->fields;
        $keys_and_values['stamp_created'] = time();
        $keys_and_values['stamp_updated'] = time();
        $re = $this->sort_fields_values($keys_and_values);

        $q = "INSERT INTO {$this->table_name} ({$re[0]}) VALUES ({$re[1]})";

        _log("DB::insert() query: $q");
        $re = $this->db->query($q);
        $this->clean();
        if ( ! $re ) {
            _log("DB::insert()  error no: " . $this->db->errno . ". error message: " . $this->db->error);
            return false;
        } else {
            return $this->db->insert_id;
        }
    }


    /**
     *
     *
     * @return bool
     *  on success - TRUE
     *  on failure - FALSE will be return and error message is logged on debug log.
     *
     * @example
     *  $re = db()->table('tests')->record(['name' => 'eunsu', 'address' => 'DongHae City'])->where("idx=$idx")->update();
     *  $re ? testOk("update ok. idx: $idx") : testBad('update failed');
     */
    public function update()
    {
        $sets = [];
        $this->fields['stamp_updated'] = time();
        foreach($this->fields as $k => $v) {
            $sets[] = "`$k`='" . $this->db->escape_string($v) ."'";
        }
        $set = implode(", ", $sets);
        $where = null;
        if ( $this->where ) $where = "WHERE {$this->where}";
        $q = "UPDATE {$this->table_name} SET $set $where";
        _log("DB::update() query: $q");
        $re = $this->db->query($q);
        $this->clean();
        if ( ! $re ) {
            _log("DB::delete() error no: " . $this->db->errno . ". error message: " . $this->db->error);
            return false;
        } else {
            return $re;
        }

    }

    /**
     *
     * @return bool
     *  on success - TRUE
     *  on failure - FALSE will be return and error message is logged on debug log.
     *
     * @return bool
     *
     * @example
     *  $re = db()->table('tests')->where(" idx < 10 AND name='jaeho' ")->delete();
     *  $re ? testOk("delete ok. re: $re") : testBad('delete failed');

     */
    public function delete() {
        $q = "DELETE FROM {$this->table_name} WHERE $this->where";
        _log("DB::delete() query: $q");
        $re = $this->db->query($q);
        $this->clean();
        if ( ! $re ) {
            _log("DB::delete() error no: " . $this->db->errno . ". error message: " . $this->db->error);
            return false;
        } else {
            return $re;
        }
    }

    private function clean() {

        $this->where = null;
        $this->fields = null;
        $this->table_name = null;
    }

    private function sort_fields_values($data)
    {
        $cols = implode(',', array_keys($data));
        foreach (array_values($data) as $value)
        {
            isset($vals) ? $vals .= ',' : $vals = '';
            $vals .= '\''.$this->db->real_escape_string($value).'\'';
        }
        return [ $cols, $vals ];
//        $mysqli->real_query('INSERT INTO '.$table.' ('.$cols.') VALUES ('.$vals.')');
    }

}


$_simplest_db = new SimplestDB();

/**
 * @return SimplestDB
 */
function db() {
    global $_simplest_db;
    return $_simplest_db;
}

