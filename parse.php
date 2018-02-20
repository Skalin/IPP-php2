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

	/**
	 * Class Parser
	 */
	class Parser {

		/**
		 * @var bool
		 */
		private $hF;
		/**
		 * @var bool
		 */
		private $sF;
		/**
		 * @var bool
		 */
		private $lF;
		/**
		 * @var bool
		 */
		private $cF;
		/**
		 * @var string
		 */
		private $statsFile;

		/**
		 * Parser constructor.
		 */
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
		/**
		 * @return array|string
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

		/**
		 *
		 */
		public function printHelp() {
			if ($this->getHF()) {
				echo "Napoveda\n";
				exit(0);
			}
		}

		/**
		 * @param $argc
		 * @param $argv
		 */
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
	}


	/**
	 * Class Token
	 */
	class Token {

		/**
		 * @var
		 */
		private $type;
		/**
		 * @var null
		 */
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
		 * @param mixed $type
		 */
		public function setType($type) {
			$this->type = $type;
		}

		/**
		 * @return null
		 */
		public function getContent() {
			return $this->content;
		}

		/**
		 * @param null $content
		 */
		public function setContent($content) {
			$this->content = $content;
		}

	}

	/**
	 * Class Lex
	 */
	class Lex {

		/**
		 * @var int
		 */
		private $comments;
		/**
		 * @var int
		 */
		private $loc;

		/**
		 * @var
		 */
		private $arrayOfLines;
		/**
		 * @var
		 */
		private $statsFlag;
		/**
		 * @var array
		 */
		private $tokenArray;
		/**
		 * @var array
		 */
		private $arrayOfInstructions = array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS",
			"POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STR2INT",
			"READ", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP", "JUMPIFEQ",
			"JUMPIFNEQ", "DPRINT", "BREAK");


		/**
		 * Lex constructor.
		 *
		 * @param $arrayOfLines
		 * @param $statsFlag
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

		/**
		 * @param $array
		 * @return array
		 */
		private function cleanArray($array) {
			$cleanedArray = array();

			foreach ($array as $key => $word) {
				if ($word != "") {
					array_push($cleanedArray, $word);
				}
			}

			return $cleanedArray;
		}

		/**
		 * @param $array
		 * @return array
		 */
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


		/**
		 * @return array
		 */
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
					$rowArray = explode(" ", $row);
					$rowArray = $this->splitComments($rowArray);

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
							case (preg_match('/(string|bool|int)@[%|_|-|\$|&|\*|A-z]?[%|_|-|\$|&|\*|A-z|0-9]*/', $rowArray[$i]) ? true : false):
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
					array_push($this->tokenArray, new Token("NEWLINE"));
					$this->setLoc($this->getLoc()+1);
				}
			}
			return $this->tokenArray;
		}
	}


	/**
	 * Class Syntax
	 */
	class Syntax {

		/**
		 * @var array
		 */
		private $syntaxRules = array(
			"MOVE" => array("VAR", "SYMB"),
			"CREATEFRAME" => array(),
			"PUSHFRAME" => array(),
			"POPFRAME" => array(),
			"DEFVAR" => array("VAR"),
			"CALL" => array("JUMPLABEL"),
			"RETURN" => array(),
			"PUSHS" => array("SYMB"),
			"POPS" => array("VAR"),
			"ADD" => array("VAR", "SYMB", "SYMB"),
			"SUB" => array("VAR", "SYMB", "SYMB"),
			"MUL" => array("VAR", "SYMB", "SYMB"),
			"IDIV" => array("VAR", "SYMB", "SYMB"),
			"LT" => array("VAR", "SYMB", "SYMB"),
			"GT" => array("VAR", "SYMB", "SYMB"),
			"EQ" => array("VAR", "SYMB", "SYMB"),
			"AND" => array("VAR", "SYMB", "SYMB"),
			"OR" => array("VAR", "SYMB", "SYMB"),
			"NOT" => array("VAR", "SYMB", "SYMB"),
			"INT2CHAR" => array("VAR", "SYMB"),
			"STR2INT" => array("VAR", "SYMB", "SYMB"),
			"READ" => array("VAR", "CONSTANT"),
			"WRITE" => array("SYMB"),
			"CONCAT" => array("VAR", "SYMB", "SYMB"),
			"STRLEN" => array("VAR", "SYMB"),
			"GETCHAR" => array("VAR", "SYMB", "SYMB"),
			"SETCHAR" => array("VAR", "SYMB", "SYMB"),
			"TYPE" => array("VAR", "SYMB"),
			"LABEL" => array("CONSTANT"),
			"JUMP" => array("JUMPLABEL"),
			"JUMPIFEQ" => array("VAR", "SYMB", "SYMB"),
			"JUMPIFNEQ" => array("VAR", "SYMB", "SYMB"),
			"DPRINT" => array("SYMB"),
			"BREAK" => array()
		);

		/**
		 * @var Token[]
		 */
		private $arrayOfTokens;

		/**
		 * Syntax constructor.
		 *
		 * @param $tokenArray
		 */
		public function __construct($tokenArray) {
			$this->arrayOfTokens = $tokenArray;
		}

		/**
		 * @param Token[] $arrayOfTokens
		 */
		private function cleanDuplicateNewLines($arrayOfTokens) {
			$cleanedArray = array();

			$previousTokenType = $arrayOfTokens[0]->getType();
			foreach ($arrayOfTokens as $token) {
				if ($token->getType() == "NEWLINE" && $previousTokenType == "NEWLINE"){
					// skip (it is easier this way)
				} else {
					array_push($cleanedArray, $token);
				}
				$previousTokenType = $token->getType();
			}


			return $cleanedArray;
		}

		/**
		 * @param Token[] $tokenArray
		 * @param $start
		 * @param $amount
		 * @return bool
		 */
		private function checkArguments($tokenArray, $start, $amount) {

			if (count($this->getRules($tokenArray[$start])) != $amount) {
				throwException(21, "SYNTAX error analysis!", true);
			} else {
				echo "VSTUP: ".$tokenArray[$start]->getType()."\n";
				echo "PRAVIDLO: ".$this->getRules($tokenArray[$start])."\n";
				for ($i = 1; $i < $amount; $i++) {
				}
			}


			return true;
		}

		/**
		 *
		 */
		public function analyse() {

			$this->arrayOfTokens = $this->cleanDuplicateNewLines($this->arrayOfTokens);

			$amountOfArguments = 0;
			for ($i = 0; $i < count($this->arrayOfTokens); $i++) {
				if ($this->arrayOfTokens[$i]->getType() == "INSTRUCTION") {
					$amountOfArguments = $this->getAmountOfArguments($this->arrayOfTokens[$i]);
					if (!($this->checkArguments($this->arrayOfTokens, $i, $amountOfArguments))) {
						throwException(21, "SYNTAX error analysis!", true);
					}
					$i += $amountOfArguments;
				}
			}
		}


		/**
		 * @param Token $inputToken
		 * @return bool|mixed
		 */
		private function getRules(Token $inputToken) {
			foreach ($this->syntaxRules as $key => $rules) {
				if ($key == $inputToken->getContent()) {
					return ($rules);
				}
			}
			return false;
		}


		/**
		 * @param $token
		 */
		private function getAmountOfArguments(Token $inputToken) {

			foreach ($this->syntaxRules as $key => $rules) {
				if ($key == $inputToken->getContent()) {
					return count($rules);
				}
			}
			return -1;
		}
	}


	/**
	 * Class XML
	 */
	class XML {

		/**
		 * @var Token[]
		 */
		private $instructions;

		/**
		 * XML constructor.
		 *
		 * @param $instructions
		 */
		public function __construct($instructions) {
			$this->instructions = $instructions;
		}

		/**
		 * @param $string
		 */
		private function convertStringLiterals($string) {
			str_replace("<", "&lt;", $string);
			str_replace(">", "&gt;", $string);
			str_replace("&", "&amp;", $string);
		}

		/**
		 * @return mixed
		 */
		public function generateXml() {
			$instructions = $this->instructions;
			$xmlProgram = new SimpleXMLElement("<program></program>");
			$i = 0;
			$instructionIterator = 1;
			$argumentIterator = 1;
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
					} else {
						$arg = "arg".$argumentIterator;
						$xmlArgument = $xmlInstruction->addChild($arg);
						$xmlArgument->addAttribute('type', $instructions[$i]->getType());
						$xmlArgument->addAttribute('content', $instructions[$i]->getContent());
						echo $xmlProgram->asXML();
					}
					$i++;
				} else {
					echo "IS NEW LINE\n";
					while($i > 0) {
						array_shift($instructions);
						$i--;
					}
					$argumentIterator = 0;
				}
			}
			return $xmlProgram->asXML();
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

	//var_dump($tokens);

	$syntax = new Syntax($tokens);
	$syntax->analyse();

	$xml = new XML($tokens);
	//$xml->generateXml();

	//var_dump($tokens);

	exit(0);