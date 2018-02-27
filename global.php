<?php
	/**
	 * Project: IPP
	 * User: skalin
	 * Date: 20.2.18
	 * Time: 16:36
	 */

	/**
	 * Function for throwing exceptions and stopping the script
	 *
	 * @param int $errorCode selector of type of error
	 * @param string $errorText Depending on this value function selects which type i will echo
	 * @param bool $talkative value selects whether to echo error or not.
	 */

	function throwException($errorCode, $errorText, $talkative) {
		global $fileName;
		if ($talkative) {
			fwrite(STDERR, "ERROR: ".$errorText."\n");
			fwrite(STDERR,"Please, consider looking for help, run script as: ".__DIR__."/".$fileName." --help\n");
		}
		exit($errorCode);
	}
