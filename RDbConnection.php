<?php

class RDbConnection extends CDbConnection
{
	/**
	 * Creates a command for execution.
	 * @param mixed $query the DB query to be executed. This can be either a string representing a SQL statement,
	 * or an array representing different fragments of a SQL statement. Please refer to {@link CDbCommand::__construct}
	 * for more details about how to pass an array as the query. If this parameter is not given,
	 * you will have to call query builder methods of {@link CDbCommand} to build the DB query.
	 * @return RDbCommand the DB command
	 */
	public function createCommand($query=null)
	{
		$this->setActive(true);
		return new RDbCommand($this,$query);
	}
}