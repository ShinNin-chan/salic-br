<?php

/**
 * @author: Wouerner <wouerner@gmail.com>
 * @author: Vinicius Feitosa da Silva <viniciusfesil@gmail.com>
 * @author: Jorge Luiz Junior <juninhoecb@gmail.com>
 * @since: 17/08/16 19:53
 */
class MinC_Db_Table_Row extends Zend_Db_Table_Row
{
    public function __get($columnName){
        try{
            return parent::__get($columnName);
        }catch(Zend_Db_Table_Row_Exception $ex){
            return parent::__get(strtolower($columnName));
        }
    }
}
