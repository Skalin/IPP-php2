<?php
	/**
	 * Project: IPP-php2
	 * User: Dominik Skala (xskala11)
	 * Date: 11.2.18
	 * Time: 16:44
	 */



	class Parser {

		private $arguments = array("hF" => false, "sF" => false, "cF" => false, "lF" => false,);
		private $statsFile = "";

		/**
		 * Function for throwing exceptions and stopping the script
		 *
		 * @param int $errorCode selector of type of error
		 * @param string $errorText Depending on this value function selects which type i will echo
		 * @param bool $echo value selects whether to echo error or not.
		 */
		protected function throwException($errorCode, $errorText, $echo) {
			global $fileName;
			if ($echo == true) {
				fwrite(STDERR, "ERROR: ".$errorText."\n");

				fwrite(STDERR,"Please, consider looking for help, run script with: ".$fileName."--help\n");
			}
			exit($errorCode);
		}

		/*
		 * Function is reading the text from STDIN
		 *
		 * @return string $input Text from STDIN is converted into string and returned, if no chars were read, function returns blank string
		 */
		public function readFromStdinToInput() {
			$input = "";
			while (($char = fgetc(STDIN)) !== false) {
				$input = $input . $char;
			}
			return $input;
		}

		public function printHelp() {
			if ($this->arguments["hF"]) {
				echo "Napoveda\n";
				exit(0);
			}
		}

		public function parseArguments($argc, $argv) {
			if ($argc != 1) {
				for ($i = 1; $i < $argc; $i++) {
					echo "Curr. arg i: ".$i."\nCurr. arg: ".$argv[$i]."\n";
					if (preg_match("/--help/", $argv[$i]) == 1 && $this->arguments["hF"] != true) {
						$this->arguments["hF"] = true;
					} else if (preg_match("/--stats=.*/", $argv[$i]) == 1) {
						$this->arguments["sF"] = true;
						$this->statsFile = substr($argv[$i], 8);
					} else if (preg_match("/--loc/", $argv[$i]) == 1) {
						$this->arguments["lF"] = true;
					} else if (preg_match("/--comments/", $argv[$i]) == 1) {
						$this->arguments["cF"] = true;
					} else {
						$this->throwException(10, "Wrong usage of arguments!", true);
					}
				}
				if (($this->arguments["lF"] || $this->arguments["cF"]) && $this->arguments["sF"] == false) {
					$this->throwException(10, "Wrong usage of arguments!", true);
				} else if (($this->arguments["lF"] || $this->arguments["cF"] || $this->arguments["sF"]) && $this->arguments["hF"] == true) {
					$this->throwException(10, "Wrong usage of arguments!", true);
				}
			}
			echo "Stats: ".$this->statsFile."\n";
		}

		private function convertStringLiterals($string) {
			str_replace("<", "&lt;", $string);
			str_replace(">", "&gt;", $string);
			str_replace("&", "&amp;", $string);
		}

		public function generateXml($arraysOfTokens) {
			$prestring = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

			if ($arraysOfTokens[0] != ".IPPcode18") {
				$this->throwException(21, "Wrong syntax of code", true);
			} else {
				$prestring = $prestring."<program language=\"IPPcode18\"";
				array_shift($arraysOfTokens);
			}
			$poststring = "<\/program>";


			foreach ($arrayOfTokens as $key => $token) {
				$prestring = $prestring."<instruction order=\"".$key."\" opcode=\"".strtoupper($token)."\"";


				$poststring = $poststring."<\/instruction>";
			}
		}

	}

	class Token {
		private $type;
		private $content;

		/**
		 * Token constructor.
		 *
		 * @param $type
		 * @param $content
		 */
		public function __construct($type, $content = NULL) {
			$this->type = $type;
			$this->content = $content;
		}

		/**
		 * @return mixed
		 */
		public function getType() {
			return $this->type;
		}

		/**
		 * @return null
		 */
		public function getContent() {
			return $this->content;
		}




	}

	class Lex {

		private $arrayOfLines;
		private $statsFlag;
		private $tokenArray;


		/**
		 * Lex constructor.
		 */
		public function __construct($arrayOfLines, $statsFlag) {
			$this->arrayOfLines = $arrayOfLines;
			$this->statsFlag = $statsFlag;
			$this->tokenArray = array();
		}

		public function analyse() {

			foreach ($this->arrayOfLines as $line) {
				$row = preg_replace('/\s+/', ' ',$line['message']);
				$rowArray = explode(" ", $row);
			}
		}
	}



	$parser = new Parser();

	$parser->parseArguments($argc, $argv);
	$parser->printHelp();

	$input = $parser->readFromStdinToInput();




	//var_dump($input);

	$parsedRows = explode("\n", $input);
	array_pop($parsedRows);

	foreach ($parsedRows as $rows) {

	}


	exit(0);

?>