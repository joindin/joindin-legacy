<?php

abstract class ApiController {
	abstract public function handle($request, $db);
}
