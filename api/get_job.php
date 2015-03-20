<?php
require_once("../include/includes.php");

if (! isset($_GET['id'])) throw new Exception('bad request', 500);

exit(json_encode(linkedin_get_job_by_id($_GET['id'])));