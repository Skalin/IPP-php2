<?php
	/**
	 * Project: IPP-php2
	 * User: Dominik Skala (xskala11)
	 * Date: 11.2.18
	 * Time: 16:44
	 */


	/**
	 * Function for throwing exceptions and stopping the script
	 *
	 * @param int $errorCode selector of type of error
	 * @param string $errorText Depending on this value function selects which type i will echo
	 * @param bool $echo value selects whether to echo error or not.
	 */
	function throwException($errorCode, $errorText, $echo) {
	global $fileName;
	if ($echo == true) {
		fwrite(STDERR, "ERROR: ".$errorText."\n");

		fwrite(STDERR,"Please, consider looking for help, run script with: ".$fileName."--help\n");
	}
	exit($errorCode);
	}

	class Parser {

		private $arguments = array("hF" => false, "sF" => false, "cF" => false, "lF" => false,);
		private $statsFile = "";

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
						throwException(10, "Wrong usage of arguments!", true);
					}
				}
				if (($this->arguments["lF"] || $this->arguments["cF"]) && $this->arguments["sF"] == false) {
					throwException(10, "Wrong usage of arguments!", true);
				} else if (($this->arguments["lF"] || $this->arguments["cF"] || $this->arguments["sF"]) && $this->arguments["hF"] == true) {
					throwException(10, "Wrong usage of arguments!", true);
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
				throwException(21, "Wrong syntax of code", true);
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

		public $comments = 0;

		private $arrayOfLines;
		private $statsFlag;
		private $tokenArray;
		private $arrayOfInstructions = array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS",
			"POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STR2INT",
			"READ", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP", "JUMPIFEQ",
			"JUMPIFNEQ", "DPRINT", "BREAK");


		/**
		 * Lex constructor.
		 */
		public function __construct($arrayOfLines, $statsFlag) {
			$this->arrayOfLines = $arrayOfLines;
			$this->statsFlag = $statsFlag;
			$this->tokenArray = array();
		}

		/**
		 * @return mixed
		 */
		public function getComments() {
			return $this->comments;
		}

		/**
		 * @param mixed $comments
		 */
		public function setComments($comments) {
			$this->comments = $comments;
		}

		public function analyse() {
			foreach ($this->arrayOfLines as $line) {
				$row = preg_replace('/\s+/', ' ',$line);
				$rowArray = explode(" ", $row);

				$commentFlag = false;
				var_dump($rowArray);
				for ($i = 0; $i < count($rowArray); $i++) {
					switch($rowArray[$i]) {
						case in_array(strtoupper($rowArray[$i]), $this->arrayOfInstructions):
							$token = new Token(strtoupper($rowArray[$i]));
							array_push($this->tokenArray, $token);
							break;
						case (preg_match('/(LF|TF|GF)@(.+)/', $rowArray[$i]) ? true : false):
							$token = new Token("VARIABLE", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						case (preg_match('/(string|bool|int)@(.+)/', $rowArray[$i]) ? true : false):
							$token = new Token("CONSTANT", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						case (preg_match('/#(.*)/', $rowArray[$i]) ? true : false):
							$this->setComments($this->getComments()+1);
							// comments
							break 2;
						default:
							echo "jumping here";
							throwException(21, "LEX ERROR Analysis!",true);
							break;
					}
				}



			}
			var_dump($this->tokenArray);
		}
	}



	$parser = new Parser();

	$parser->parseArguments($argc, $argv);
	$parser->printHelp();

	$input = $parser->readFromStdinToInput();
	$arrayOfLines = explode("\n", $input);
	array_pop($arrayOfLines);
	$lex = new Lex($arrayOfLines, true);

	$lex->analyse();



	exit(0);

?>