<?
require_once("/home/bocawebgroup/public_html/inc/config.inc");

class dbi{
	// Initialize Variables
	var $link,$queryresource="",$row="",$loop="";

	// Constructor
	function dbi(){
		global $dbh;
		$this->row=0;
		$this->loop=0;
		// Open connection to DB Server
		$this->link = $dbh;
		if(!$this->link)
			die( "Couldn't connect to Database Server\n" );
	}

	// This will be replaced once the Zend 2 Engine comes out
	function destruct(){
		return mysql_close($this->link);
	}

	// Query Function
	function query($myquery){
		$this->row = $this->loop = 0;
		$this->queryresource = mysql_query($myquery, $this->link)
			or die("Failed Query: ".mysql_error());
		// check to see if it works
		if(!$this->queryresource)
			die("Failed Query: ".mysql_error());
		if(preg_match("/^select.*$/",strtolower($myquery))){
			// Return number of rows for Select Statement
			return mysql_num_rows($this->queryresource);
		}elseif(preg_match("/^insert.*$/",strtolower($myquery))){
			// Return Inserted ID for Insert Statement
			return mysql_insert_id($this->link);
		}else{
			// All others return affected rows
			return mysql_affected_rows($this->link);
		}
	}

	// This will return the query resource if you need to do some custom
	// routine not specfied in the DBI
	function getqueryresource(){
		return $this->queryresource;
	}

	// Returns the result of a column in the query statement
	function result($column){
		return mysql_result($this->queryresource,$this->row,$column);
	}

	// Loop through the returned results.  You can use result() within the loop.
	// To get the results of a column on the current row.
	function loop(){
		if(!mysql_num_rows($this->queryresource))
			return FALSE;
		if($this->loop==0){
			$this->loop = 1;
			return TRUE;
		}elseif($this->row + 1 < $this->numrows()){
			$this->row = $this->row + 1;
			return TRUE;
		}else{
			$this->row=0;
			$this->loop=0;
			return FALSE;
		}
	}

	// Returns the number of rows in a select statement
	function numrows(){
		if(is_resource($this->queryresource))
			return mysql_num_rows($this->queryresource);
		else
			return 0;
	}

	// Returns the currentrow result() will return
	function currentrow(){
		return $this->row;
	}

}
?>
