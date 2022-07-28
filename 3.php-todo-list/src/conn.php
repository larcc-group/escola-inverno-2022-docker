<?php
if (getenv('MYSQL_HOST')) include('mysql.php');
if (getenv('REDIS_HOST')) include('redis.php');