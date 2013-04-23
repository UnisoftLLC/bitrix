<?php

/*  Aбстактный класс с общими методами для доступа ко всем 
 *  таблицам в рамках модуля подгрузка остатков
 *  Aвтор - Александр Кудин 
 *  kudinsasha@gmail.com
 *  04.04.2013  
 *  
 *  Не реккомендуется просмотр людям, лишённым шизофрении 
 */


abstract class mytpl{
 
    public function Update($id, $arr) { 
        $id = intval($id);
        if (!$id || !$arr || !is_array($arr))
            return false;  
        global $DB;
        $f = array(); 
        foreach ($arr as $k => $v)
            if (in_array($k, array_keys($this->fields)))
                $f[] = '`' . $k . '` = "' . $DB->ForSql($v) . '" ';   
        if (!count($f))
            return false;
        $result = $DB->Query( 'UPDATE `'. $this->tablename .
                            '` SET '. implode(',', $f) . 
                            '  WHERE `ID` = '. $id .';'); 
        return $result; 
    }
    
    function GetList($order, $filter, $t = 0){
        global $DB;      
        if (!$order)
            $order = array('ID' => 'ASC'); 
        foreach ($order as $k => $v) { 
            $v = strtoupper($v) == 'ASC' ? $v : 'DESC';
            if (in_array($k, array_keys($this->fields)))
                $o[] = '`' . $DB->ForSql($k) . '` ' . $v;
        }
        $order = implode(', ', $o);
        $f = array('1=1');  
        foreach ($filter as $k => $v) {
            if (in_array($k, array_keys($this->fields))) {
                if (is_array($v)) {
                    $filTmp = array();
                    foreach ($filter[$k] as $id)
                        $filTmp[] = '`' . $k . '` = "' . $DB->ForSql($id) . '"';
                    $f[] = '(' . implode(') OR (', $filTmp) . ')';
                } else {
                    $f[] = '`' . $k . '` = "' . $DB->ForSql($v) . '"';
                } 
            }
        }
        $where = '(' . implode(') AND (', $f) . ')'; 
        $strSql = 'SELECT * FROM `' . $this->tablename . '`
                  ' . ($where ? ' WHERE ' . $where : '') . '
                  ' . ($order ? ' ORDER BY ' . $order : '') . ';';
        if($t == 1){
            echo $strSql; die();
        }
        $rs = $DB->Query($strSql); 
        return $rs;
    } 
   
    function RemoveAll() {
        global $DB;
        $DB->Query("DELETE FROM `" . $this->tablename . "`");
    } 
     
    function RemoveByID($id) {
        $id = intval($id);
        if ($id <= 0)
            return false;
        global $DB; 
        $DB->Query("DELETE FROM `" . $this->tablename . "` WHERE `ID` = {$id}");
    }
    
    function GetByID($id){
        return $this->GetList(array(), array('ID' => $id));
    }
    
    function GetAll($arOrder = array("ID"=>"DESC")){
        return $this->GetList($arOrder, array());
    }
    
}
