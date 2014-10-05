<?php
	
	class Database {
		public static function connect() {
			mysql_select_db(
				TS_DATABASE_DBNAME,
				mysql_connect(
					TS_DATABASE_SERVER,
					TS_DATABASE_USERNAME,
					TS_DATABASE_PASSWORD
				)
			);
		}

	}