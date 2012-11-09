<?php

class RDbCommand extends CDbCommand
{
	/**
	 * Creates and executes an REPLACE SQL statement.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $columns the column data (name=>value) to be inserted into the table
	 *			 or array(array(name=>value),array(name=>value)).
	 * @return integer number of rows affected by the execution.
	 */
	public function replace($table, $columns)
	{
		return $this->_insertReplace($table, $columns, array(), '', 'REPLACE');
	}
	
	/**
	 * Creates and executes an INSERT SQL statement.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $columns the column data (name=>value) to be inserted into the table
	 *			 or array(array(name=>value),array(name=>value)).
	 * @return integer number of rows affected by the execution.
	 */
	public function insert($table, $columns)
	{
		return $this->insertUpdate($table, $columns);
	}

	/**
	 * Creates and executes an INSERT IGNORE SQL statement.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $columns the column data (name=>value) to be inserted into the table
	 *			 or array(array(name=>value),array(name=>value)).
	 * @return integer number of rows affected by the execution.
	 */
	public function insertIgnore($table, $columns)
	{
		return $this->insertUpdate($table, $columns, array(), 'IGNORE');
	}

	/**
	 * Creates and executes an INSERT ... ON DUPLICATE KEY UPDATE SQL statement.
	 * The method will properly escape the column names, and bind the values to be inserted.
	 * @param string $table the table that new rows will be inserted into.
	 * @param array $columns the column data (name=>value) to be inserted into the table
	 *			 or array(array(name=>value),array(name=>value)).
	 * @param array $update the column data (name=>value) to be update
	 * @param string $ignore '' or 'IGNORE'
	 * @return integer number of rows affected by the execution.
	 */
	public function insertUpdate($table, $columns, $update=array(), $ignore='')
	{
		return $this->_insertReplace($table, $columns, $update, $ignore);
	}
	
	protected function _insertReplace($table, $columns, $update=array(), $ignore='', $type='INSERT')
	{
		list($names, $placeholders, $params) = $this->_insert($columns);

		$sql="{$type} {$ignore} INTO " . $this->getConnection()->quoteTableName($table)
			. ' (' . implode(', ',$names) . ') VALUES '
			. implode(', ', $placeholders);
		
		list($lines, $params) =  $this->_updatePrepare($update, $params);
		if($lines)
		{
			$sql .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $lines);
		}
		return $this->setText($sql)->execute($params);
	}

	/**
	 * Prepare $names, $placeholders, $params
	 * @param array $columns
	 * @return array array($names, $placeholders, $params)
	 */
	protected function _insert($columns)
	{
		$params=array();
		$names=array();
		$placeholders=array();
		if(is_array(reset($columns)))
		{
			foreach($columns as $i=>$column)
			{
				list($names, $placeholders[], $params) = $this->_insertPrepare($column, $i, $params);
			}
		}
		else
		{
			list($names, $placeholders[], $params) = $this->_insertPrepare($columns, $params);
		}

		return array($names, $placeholders, $params);
	}

	/**
	 * Prepare $names, $placeholders, $params
	 * @param array $columns
	 * @return array array($names, $placeholders, $params)
	 */
	protected function _insertPrepare($columns, $iterat='', $params=array())
	{
		$names=array();
		$placeholders=array();
		foreach($columns as $name=>$value)
		{
			$names[]=$this->getConnection()->quoteColumnName($name);
			if($value instanceof CDbExpression)
			{
				$placeholders[] = $value->expression;
				foreach($value->params as $n => $v)
					$params[$n] = $v;
			}
			else
			{
				$name = $this->_paramName(':' . $name . '_' . $iterat, $params);
				$placeholders[] = $name;
				$params[$name] = $value;
			}
		}
		$placeholders ='(' . implode(', ', $placeholders) . ')';
		return array($names, $placeholders, $params);
	}

	/**
	 * Prepare $lines, $params
	 * @param array $columns
	 * @return array array($lines, $params)
	 */
	protected function _updatePrepare($columns, $params=array())
	{
		$lines=array();
		foreach($columns as $name=>$value)
		{
			if($value instanceof CDbExpression)
			{
				$lines[]=$this->_connection->quoteColumnName($name) . '=' . $value->expression;
				foreach($value->params as $n => $v)
					$params[$n] = $v;
			}
			else
			{
				$lines[]=$this->_connection->quoteColumnName($name) . '=:' . $name;
				$params[$this->_paramName(':' . $name, $params)]=$value;
			}
		}
		return array($lines, $params);
	}

	/**
	 * Generate unique name for param
	 * @param string $name
	 * @param array $params
	 * @return string
	 */
	protected function _paramName($name, $params)
	{
		return isset($params[$name]) ? $this->_paramName($name.'_'.rand(0,9), $params) : $name;
	}
}