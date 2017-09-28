<?php

class MinC_Db_Adapter_Pdo_Pgsql extends Zend_Db_Adapter_Pdo_Pgsql
{
    public function query($sql, $bind = array())
    {
        if (empty($bind) && $sql instanceof Zend_Db_Select) {
            $bind = $sql->getBind();
        }

        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if (!is_numeric($name) && !preg_match('/^:/', $name)) {
                    $newName = ":$name";
                    unset($bind[$name]);
                    $bind[$newName] = $value;
                }
            }
        }

        try {
            $this->_connect();
            if ($sql instanceof Zend_Db_Select) {
                if (empty($bind)) {
                    $bind = $sql->getBind();
                }
                $sql = $sql->assemble();
            }

            if (!is_array($bind)) {
                $bind = array($bind);
            }

            $sql = str_ireplace('.dbo', '', $sql);

            $stmt = $this->prepare($sql);
            $stmt->execute($bind);
            $stmt->setFetchMode($this->_fetchMode);

            return $stmt;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function treatWhereConditionsDoubleQuotes($condition)
    {
        $colunaLimpa = trim($condition);
        $separator = '=';
        if ($colunaLimpa && strpos($colunaLimpa, $separator) !== false) {
            $arrayColumn = explode($separator, $condition);
            if (strpos($arrayColumn[0], '"') === false) {
                $conditionOne = $this->addDoubleQuote(trim($arrayColumn[0]));
                $conditionTwo = trim($arrayColumn[1]);
//                $conditionTwo = $this->addSimpleQuote($arrayColumn[1]);
                $condition = "{$conditionOne} {$separator} {$conditionTwo}";
            }
        } elseif ($colunaLimpa && strpos($colunaLimpa, 'in') !== false) {
            $separator = 'in';
            $arrayColumn = explode($separator, $condition);
            if (substr(trim($arrayColumn[1]), 0, 1) == '(' && strpos($arrayColumn[0], '"') === false) {
                $conditionOne = $this->addDoubleQuote(trim($arrayColumn[0]));
                $conditionTwo = trim($arrayColumn[1]);
                $condition = "{$conditionOne} {$separator} {$conditionTwo}";
            }
        }

        return $condition;
    }

    public function treatConditionDoubleQuotes($condition)
    {
        $arrayClauses = ['and', 'AND', 'or', 'OR'];
        $newCondition = '';
        foreach ($arrayClauses as $clause) {
            $arrayConditions = explode($clause, $condition);
            if (count($arrayConditions) > 1) {
                $treatedCondition = '';
                foreach ($arrayConditions as $explodedCondition) {
                    if (!empty($treatedCondition)) {
                        $treatedCondition .= ' ' . strtoupper($clause) . ' ';
                    }
                    $treatedCondition .= $this->treatColumnWithDoubleQuote($explodedCondition);
                }
                $newCondition .= $treatedCondition;
            }
        }

        if ($newCondition) {
            $condition = $newCondition;
        } else {
            $condition = $this->treatColumnWithDoubleQuote($condition);
        }

        return $condition;
    }

    public function treatColumnWithDoubleQuote($condition)
    {
        $cleanCondition = trim($condition);
        $separator = '=';
        if ($cleanCondition && strpos($cleanCondition, $separator) !== false) {
            $arrayColunas = explode($separator, $condition);
            $coluna1 = $this->addDoubleQuote(trim($arrayColunas[0]));
            $coluna2 = $this->addDoubleQuote(trim($arrayColunas[1]));

            $arrayConditionPart2 = explode(' ', trim($arrayColunas[1]));

            if (is_numeric($arrayConditionPart2[0])) {
                $coluna2 = $arrayConditionPart2[0] + 0;
            } elseif ($arrayConditionPart2[0] == '?') {
                $coluna2 = $arrayConditionPart2[0];
            }
            $condition = "{$coluna1} {$separator} {$coluna2}";
            if (isset($arrayConditionPart2[1])) {
                $condition += " {$arrayConditionPart2[1]}";
            }
        } elseif ($cleanCondition && strpos($cleanCondition, 'in') !== false) {
            $separator = 'in';
            $arrayColumn = explode($separator, $condition);
            if (substr(trim($arrayColumn[1]), 0, 1) == '(' && strpos($arrayColumn[0], '"') === false) {
                $conditionOne = $this->addDoubleQuote(trim($arrayColumn[0]));
                $conditionTwo = trim($arrayColumn[1]);
                $condition = "{$conditionOne} {$separator} {$conditionTwo}";
            }
        }

        return $condition;
    }

    protected function addDoubleQuote($field)
    {
        if (substr(trim($field), 0, 1) != '"') {
            $hasDot = strpos($field, '.');
            if ($hasDot !== false) {
                $fieldPieces = explode('.', $field);
                $field = '';
                foreach ($fieldPieces as $fieldPiece) {
                    if (!empty($field)) {
                        $field .= '.';
                    }
                    $field .= $this->addDoubleQuote($fieldPiece);
                }
            } elseif (strpos($field, "||") !== false) {
                $fieldPieces = explode('||', $field);
                $field = '';
                foreach ($fieldPieces as $fieldPiece) {
                    if (!empty($field)) {
                        $field .= ' || ';
                    }
                    $field .= $this->addDoubleQuote($fieldPiece);
                }
            } elseif (!is_numeric($field)
                        && strpos($field, "'") === false
                        && $field != "false"
                        && $field != "true") {
                if (strpos($field, '::') !== false) {
                    $field = '"'
                            . substr($field, 0, strpos($field, '::'))
                            . '"'
                            . substr($field, strpos($field, '::'));
                } elseif (strpos($field, 'to_char(') !== false) {
                    $field = 'to_char("'
                            . substr($field, strpos($field, 'to_char(') + 1, strpos($field, ','))
                            . '"'
                            . substr($field, strpos($field, ','));
                } else {
                    $field = '"' . trim($field) . '"';
                }
            }
        }

        return $field;
    }

    protected function addSimpleQuote($field)
    {
        $field = trim($field);
        if (strpos($field, "'") === false
            && strpos($field, '"') === false
            && strpos($field, '(') === false
            && strpos($field, ')') === false
        ) {
            $field = "'{$field}'";
        }

        return $field;
    }

    public function treatColumnsDoubleQuotes($columns)
    {
        if (is_array($columns)) {
            foreach ($columns as &$column) {
                $columnParts = explode(' as ', $column);
                if (count($columnParts) > 1) {
                    $columnFirstPart = $this->addDoubleQuote($columnParts[0]);
                    $columnSecondPart = ' as ' . $this->addDoubleQuote($columnParts[1]);
                    $column = new Zend_Db_Expr($columnFirstPart . $columnSecondPart);
                } else {
                    $column = new Zend_Db_Expr($this->addDoubleQuote($column));
                }
            }
        } elseif (!is_null($columns) && !empty($columns)) {
            $columns = new Zend_Db_Expr($this->addDoubleQuote($columns));
        }

        return $columns;
    }

    protected function _quoteIdentifierTable($ident, $alias = null, $auto = false, $as = ' AS ')
    {
        if ($ident instanceof Zend_Db_Expr) {
            $quoted = $ident->__toString();
        } elseif ($ident instanceof Zend_Db_Select) {
            $quoted = '(' . $ident->assemble() . ')';
        } else {
            $quoted = $this->_quoteIdentifier($ident, $auto);
        }
        if ($alias !== null) {
            $quoted .= $as . $this->_quoteIdentifier($alias, $auto);
        }
        return $quoted;
    }

    public function quoteTableAs($ident, $alias = null, $auto = false)
    {
        return $this->_quoteIdentifierTable($ident, $alias, $auto);
    }
}
