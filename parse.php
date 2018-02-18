<?php
	/**
	 * Project: IPP-php2
	 * User: Dominik Skala (xskala11)
	 * Date: 11.2.18
	 * Time: 16:44
	 */


	$fileName = "parse.php";
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

	class Parser {

		private $hF;
		private $sF;
		private $lF;
		private $cF;
		private $statsFile;

		public function __construct() {
			$this->statsFile = "";
			$this->hF = false;
			$this->sF = false;
			$this->lF = false;
			$this->cF = false;
		}

		/**
		 * @return mixed
		 */
		public function getHF() {
			return $this->hF;
		}

		/**
		 * @param mixed $hF
		 */
		public function setHF($hF) {
			$this->hF = $hF;
		}

		/**
		 * @return mixed
		 */
		public function getSF() {
			return $this->sF;
		}

		/**
		 * @param mixed $sF
		 */
		public function setSF($sF) {
			$this->sF = $sF;
		}

		/**
		 * @return mixed
		 */
		public function getLF() {
			return $this->lF;
		}

		/**
		 * @param mixed $lF
		 */
		public function setLF($lF) {
			$this->lF = $lF;
		}

		/**
		 * @return mixed
		 */
		public function getCF() {
			return $this->cF;
		}

		/**
		 * @param mixed $cF
		 */
		public function setCF($cF) {
			$this->cF = $cF;
		}



		/**
		 * @return string
		 */
		public function getStatsFile() {
			return $this->statsFile;
		}

		/**
		 * @param string $statsFile
		 */
		public function setStatsFile($statsFile) {
			$this->statsFile = $statsFile;
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


			$input = explode("\n", $input);
			array_pop($input);
			return $input;
		}

		public function printHelp() {
			if ($this->getHF()) {
				echo "Napoveda\n";
				exit(0);
			}
		}

		public function parseArguments($argc, $argv) {
			if ($argc != 1) {
				for ($i = 1; $i < $argc; $i++) {
					if (preg_match("/--help/", $argv[$i]) == 1 && $this->getHF() != true) {
						$this->setHF(true);
					} else if (preg_match("/--stats=.*/", $argv[$i]) == 1) {
						$this->setSF(true);
						$this->setStatsFile(substr($argv[$i], 8));
					} else if (preg_match("/--loc/", $argv[$i]) == 1) {
						$this->setLF(true);
					} else if (preg_match("/--comments/", $argv[$i]) == 1) {
						$this->setCF(true);
					} else {
						throwException(10, "Wrong usage of arguments!", true);
					}
				}
				if (($this->getLF() || $this->getCF()) && $this->getSF() == false) {
					throwException(10, "Wrong usage of arguments!", true);
				} else if (($this->getLF() || $this->getCF() || $this->getSF()) && $this->getHF() == true) {
					throwException(10, "Wrong usage of arguments!", true);
				} else if ($this->getSF() && $this->getStatsFile() == "") {
					throwException(10, "Wrong stats file location!", true);
				}
			}

			echo "Help: ".$this->getHF()."\n";
			echo "Stats: ".$this->getSF()."\n";
			if ($this->getSF()) {
				echo "File Location: ".$this->getStatsFile()."\n";
			}
			echo "Loc: ".$this->getLF()."\n";
			echo "Comments: ".$this->getCF()."\n";
		}

		private function convertStringLiterals($string) {
			str_replace("<", "&lt;", $string);
			str_replace(">", "&gt;", $string);
			str_replace("&", "&amp;", $string);
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

		private $comments;
		private $loc;

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
			$this->comments = 0;
			$this->loc = 0;
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
		private function setComments($comments) {
			$this->comments = $comments;
		}

		/**
		 * @return int
		 */
		public function getLoc() {
			return $this->loc;
		}

		/**
		 * @param int $loc
		 */
		public function setLoc($loc) {
			$this->loc = $loc;
		}

		private function cleanArray($array) {
			$cleanedArray = array();

			foreach ($array as $key => $word) {
				if ($word != "") {
					array_push($cleanedArray, $word);
				}
			}

			return $cleanedArray;
		}

		private function splitComments($array) {
			$newArray = array();

			foreach ($array as $key => $word) {
				if (preg_match('/#(.*)/', $word)) {
					array_push($newArray, substr($word,0, strpos($word, "#"))); // instruction
					array_push($newArray, substr($word, strpos($word, "#"))); // comment
					break;
				} else {
					array_push($newArray, $word);
				}
			}

			$newArray = $this->cleanArray($newArray);
			return $newArray;
		}


		public function analyse() {
			if (count($this->arrayOfLines) > 0) {

				if ($this->arrayOfLines[0] == ".IPPcode18") {
					$token = new Token("PROGRAM", array_shift($this->arrayOfLines));
					array_push($this->tokenArray, $token);
				} else {
					throwException(21, "LEX ERROR Analysis!",true);
				}
				foreach ($this->arrayOfLines as $line) {
					$row = preg_replace('/\s+/', ' ',$line);
					$rowArray = explode(" ", $line);
					$rowArray = $this->splitComments($rowArray);

					$commentFlag = false;
					for ($i = 0; $i < count($rowArray); $i++) {
						switch($rowArray[$i]) {
							case in_array(strtoupper($rowArray[$i]), $this->arrayOfInstructions):
								$token = new Token("INSTRUCTION", strtoupper($rowArray[$i]));
								array_push($this->tokenArray, $token);
								break;
							case (preg_match('/(LF|TF|GF)@[%|_|-|\$|&|\*|A-z]{1}[%|_|-|\$|&|\*|A-z|0-9]+/', $rowArray[$i]) ? true : false):
								$token = new Token("VARIABLE", $rowArray[$i]);
								array_push($this->tokenArray, $token);
								break;
							case (preg_match('/(string|bool|int)@[%|_|-|\$|&|\*|A-z]{1}[%|_|-|\$|&|\*|A-z|0-9]+/', $rowArray[$i]) ? true : false):
								$token = new Token("CONSTANT", $rowArray[$i]);
								array_push($this->tokenArray, $token);
								break;
							case (preg_match('/#(.*)/', $rowArray[$i]) ? true : false):
								// comments
								$this->setComments($this->getComments()+1);
								if ($i == 0) {
									$this->setLoc($this->getLoc()-1);
								}
								break 2;
							case (preg_match('/[\%|\_|\-|\$|\&|\*|A-z]{1}[%|_|-|\$|&|\*|A-z|0-9]+/', $rowArray[$i]) ? true : false):
								$token = new Token("JUMPLABEL", $rowArray[$i]);
								array_push($this->tokenArray, $token);
								break;
							default:
								throwException(21, "LEX ERROR Analysis!",true);
								break;
						}
					}
					$token = new Token("NEWLINE");
					array_push($this->tokenArray, $token);
					$this->setLoc($this->getLoc()+1);
				}
			}
			return $this->tokenArray;
		}
	}


	class Syntax {

		private $tokens = array();

		public function __construct($tokenArray) {
			$this->tokens = $tokenArray;
		}
	}


	class XML {

		/**
		 * @var Token[]
		 */
		private $instructions;

		public function __construct($instructions) {
			$this->instructions = $instructions;
		}

		public function generateXml() {
			$instructions = $this->instructions;
			$xmlProgram = new SimpleXMLElement("<program></program>");
			$i = 0;
			$instructionIterator = 1;
			while ($i < count($instructions)) {
				if ($instructions[$i]->getType() != "NEWLINE") {
					echo "NOT NEW LINE\n";
					if ($instructions[$i]->getType() == "PROGRAM") {
						$xmlProgram->addAttribute('language', '.IPPcode18');
					} else if ($instructions[$i]->getType() == "INSTRUCTION") {
						$xmlInstruction = $xmlProgram->addChild('instruction');
						$xmlInstruction->addAttribute('order', $instructionIterator);
						$instructionIterator++;
						$xmlInstruction->addAttribute('opcode', $instructions[$i]->getContent());
					} else if (in_array($instructions[$i]->getType(), array("VARIABLE", "CONSTANT", "JUMPLABEL"))) {
						// do something
					}
					$i++;
				} else {
					while($i > 0) {
						array_shift($instructions);
						$i--;
					}
				}
			}
			return $xmlProgram->asXML();
		}

		private function countNewLines($array) {

		}

	}


	$parser = new Parser();

	$parser->parseArguments($argc, $argv);
	$parser->printHelp();

	$arrayOfLines = $parser->readFromStdinToInput();
	$lex = new Lex($arrayOfLines, true);

	$tokens = $lex->analyse();

	if ($parser->getSF()) {
		$stats = "";
		if ($parser->getLF()) {
			$stats = $stats.$lex->getLoc()."\n";
		}
		if ($parser->getCF()) {
			$stats = $stats.$lex->getComments()."\n";
		}
		echo $stats;
	}


	$syntax = new Syntax($tokens);

	$xml = new XML($tokens);
	$xml->generateXml();

	exit(0);