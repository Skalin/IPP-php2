<?php
/**
 * Project: IPP-php2
 * User: Dominik Skala (xskala11)
 * Date: 11.2.18
 * Time: 16:44
 */


class Singleton {

	private $fileName = "parse.php";

	private function __construct() {

	}

	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new Singleton();
		}
		return $inst;
	}

	/**
	 * Function for throwing exceptions and stopping the script
	 *
	 * @param int $errorCode selector of type of error
	 * @param string $errorText Depending on this value function selects which type i will echo
	 * @param bool $verbose value selects whether to echo error or not.
	 */

	public function throwException($errorCode, $errorText, $verbose) {
		if ($verbose) {
			fwrite(STDERR, "ERROR: ".$errorText."\n");
			fwrite(STDERR,"Please, consider looking for help, run script as: ".__DIR__."/".$this->fileName." --help\n");
		}
		exit($errorCode);
	}
}


/**
 * Class Parser
 */
class Parser extends Singleton {

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
	 * @var string
	 */
	private $first;

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
	 * @return string
	 */
	public function getFirst() {
		return $this->first;
	}

	/**
	 * @param string $first
	 */
	public function setFirst($first) {
		$this->first = $first;
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
			echo "parse.php - lexikalni a syntakticky analyzator v jazyce PHP 5.6\n";
			echo "Autor: Dominik Skala (xskala11)\n";
			echo "Predmet: IPP 2018\n";
			echo "Lexikalni a syntakticky analyzator jazyka IPPcode18. Nacita ze standardniho vstupu zdrojovy kod v IPPcode18, zkontroluje lexikalni a syntaktickou spravnost kodu a vypise na standardni vystup XML reprezentaci programu.\n";
			echo "Implementovana rozsireni: STATS\n\n";
			echo "Argumenty:\n";
			echo "\t--help\t\t\tTiskne tuto napovedu, nenacita zadny vstup. Nelze kombinovat s dalsimi argumenty.\n";
			echo "\t--stats=file\t\tUklada statistiky o zdrojovem kodu, ktere se sbiraji behem syntakticke a lexikalni analyzy. Vyzaduje alespon jeden z nasledujicich argumentu.\n";
			echo "\t--loc\t\t\tUklada statistiky po poctu radku s instrukcemi. Nepocitaji se prazdne radky nebo radky obsahujici pouze komentar.\n";
			echo "\t--comments\t\tUklada statistiky o poctu komentaru. Kazdy radek na kterem se nalezne komentar, je zapocitan.\n";
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
					if($this->getFirst() == "") {
						$this->setFirst("L");
					}
				} else if (preg_match("/--comments/", $argv[$i]) == 1) {
					$this->setCF(true);
					if($this->getFirst() == "") {
						$this->setFirst("C");
					}
				} else {
					$this->throwException(10, "Wrong usage of arguments!", true);
				}
			}
			if (($this->getLF() || $this->getCF()) && $this->getSF() == false) {
				$this->throwException(10, "Wrong usage of arguments!", true);
			} else if (($this->getLF() || $this->getCF() || $this->getSF()) && $this->getHF() == true) {
				$this->throwException(10, "Wrong usage of arguments!", true);
			} else if ($this->getSF() && $this->getStatsFile() == "") {
				$this->throwException(10, "Wrong stats file location!", true);
			} else if ($this->getSF() && (!$this->getCF() && !$this->getLF())) {
				$this->throwException(10, "Wrong usage of arguments!", true);
			}
		}
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
class Lex extends Singleton {

	/**
	 * @var int
	 */
	private $comments;

	/**
	 * @var
	 */
	private $arrayOfLines;

	/**
	 * @var array
	 */
	private $tokenArray;

	/**
	 * @var array
	 */
	private $arrayOfInstructions = array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS",
		"POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STRI2INT",
		"READ", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP", "JUMPIFEQ",
		"JUMPIFNEQ", "DPRINT", "BREAK");

	/**
	 * Lex constructor.
	 *
	 * @param $arrayOfLines
	 */
	public function __construct($arrayOfLines) {
		$this->arrayOfLines = $arrayOfLines;
		$this->tokenArray = array();
		$this->comments = 0;
	}

	/**
	 * @return mixed
	 */
	public function getAmountOfComments() {
		return $this->comments;
	}

	/**
	 * @param mixed $comments
	 */
	private function setAmountOfComments($comments) {
		$this->comments = $comments;
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
			if (strtoupper($this->arrayOfLines[0]) == ".IPPCODE18") {
				$token = new Token("PROGRAM", array_shift($this->arrayOfLines));
				array_push($this->tokenArray, $token);
			} else {
				$this->throwException(21, "LEX error analysis!",true);
			}
			foreach ($this->arrayOfLines as $line) {
				$row = preg_replace('/\s+/', ' ',$line);
				$rowArray = explode(" ", $row);
				$rowArray = $this->splitComments($rowArray);
				$previousToken = "";
				for ($i = 0; $i < count($rowArray); $i++) {
					switch($rowArray[$i]) {
						case in_array(strtoupper($rowArray[$i]), $this->arrayOfInstructions):
							if (preg_match('/(INSTRUCTION\.)(CALL|LABEL|JUMP|JUMPIFEQ|JUMPIFNEQ)/', $previousToken) == true) {
								if (preg_match('/^[\%|\_|\-|\$|\&|\*|A-z]{1}[%|_|-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) == true) {
									$token = new Token("LABEL", $rowArray[$i]);
								} else {
									$this->throwException(21, "LEX error analysis!", true);
								}
							} else {
								$token = new Token("INSTRUCTION", strtoupper($rowArray[$i]));
							}
							array_push($this->tokenArray, $token);
							$previousToken = $token->getType().".".$token->getContent();
							break;
						case (preg_match('/^(LF|TF|GF)@[%|_|-|$|&|\*|A-z]{1}[%|_|\-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) ? true : false):
							$token = new Token("VAR", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						case (preg_match('/^(string|bool|int)@[%|_|\-|\+|\$|&|\*|A-z]?[\S]*$/', $rowArray[$i]) ? true : false):
							$token = new Token("CONSTANT", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						case (preg_match('/#(.*)/', $rowArray[$i]) ? true : false):
							// comments
							$this->setAmountOfComments($this->getAmountOfComments()+1);
							break 2;
						case (preg_match('/^[\%|\_|\-|\$|\&|*|A-z]{1}[%|_|-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) ? true : false):
							if (preg_match('/^(int|string|bool)$/', $rowArray[$i]) == true) {
								$token = new Token("TYPE", $rowArray[$i]);
							} else {
								$token = new Token("LABEL", $rowArray[$i]);
							}
							array_push($this->tokenArray, $token);
							break;
						default:
							// TODO only int|string|bool after READ
							$this->throwException(21, "LEX error analysis!",true);
							break;
					}
				}
				array_push($this->tokenArray, new Token("NEWLINE"));
			}
		} else {
			$this->throwException(21, "LEX error analysis! Empty input!", true);
		}
		return $this->tokenArray;
	}
}


/**
 * Class Syntax
 */
class Syntax extends Singleton {

	/**
	 * @var array
	 */
	private $syntaxRules = array(
		"MOVE" => array("VAR", "SYMB"),
		"CREATEFRAME" => array(),
		"PUSHFRAME" => array(),
		"POPFRAME" => array(),
		"DEFVAR" => array("VAR"),
		"CALL" => array("LABEL"),
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
		"NOT" => array("VAR", "SYMB"),
		"INT2CHAR" => array("VAR", "SYMB"),
		"STRI2INT" => array("VAR", "SYMB", "SYMB"),
		"READ" => array("VAR", "TYPE"),
		"WRITE" => array("SYMB"),
		"CONCAT" => array("VAR", "SYMB", "SYMB"),
		"STRLEN" => array("VAR", "SYMB"),
		"GETCHAR" => array("VAR", "SYMB", "SYMB"),
		"SETCHAR" => array("VAR", "SYMB", "SYMB"),
		"TYPE" => array("VAR", "SYMB"),
		"LABEL" => array("LABEL"),
		"JUMP" => array("LABEL"),
		"JUMPIFEQ" => array("LABEL", "SYMB", "SYMB"),
		"JUMPIFNEQ" => array("LABEL", "SYMB", "SYMB"),
		"DPRINT" => array("SYMB"),
		"BREAK" => array()
	);

	/**
	 * @var array
	 */
	private $symbs = array("VAR", "CONSTANT");

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
	 * @return array
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
	 * @return integer
	 */
	private function getAmountOfArguments($tokenArray, $start) {
		$amount = 0;
		$i = $start+1; // $start = INSTRUCTION, +1 starts arguments
		while ($tokenArray[$i]->getType() != "NEWLINE") {
			$amount++;
			$i++;
		}
		return $amount;
	}

	/**
	 * @param Token[] $tokenArray
	 * @param $start
	 * @param $amount
	 * @return bool
	 */
	private function checkArguments($tokenArray, $start, $amount) {
		$amountOfArguments = $this->getAmountOfArguments($tokenArray, $start);
		if ($amountOfArguments != $amount) {
			return false;
		} else {
			$rules = $this->getRules($tokenArray[$start]);
			for ($i = 0; $i <= ($amount - 1); $i++) {
				if ($rules[$i] == "VAR" || $rules[$i] == "LABEL" || $rules[$i] == "TYPE") {
					if ($rules[$i] == $tokenArray[$start + $i + 1]->getType()) {
					} else {
						return false;
					}
				} else if ($rules[$i] == "SYMB" || $rules[$i] == "CONSTANT") {
					if (in_array($tokenArray[$start + $i + 1]->getType(), $this->symbs) || $tokenArray[$start+$i+1]->getType() == "CONSTANT") {
						if ($tokenArray[$start + $i + 1]->getType() == "CONSTANT") {
							$splitter = strpos($tokenArray[$start + $i + 1]->getContent(), "@");
							$type = substr($tokenArray[$start + $i + 1]->getContent(), 0, $splitter);
							$content = substr($tokenArray[$start + $i + 1]->getContent(), $splitter + 1);
							$tokenArray[$start + $i + 1]->setType($type);
							$tokenArray[$start + $i + 1]->setContent($content);
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 *
	 */
	public function analyse() {
		$this->arrayOfTokens = $this->cleanDuplicateNewLines($this->arrayOfTokens);
		for ($i = 0; $i < count($this->arrayOfTokens); $i++) {
			if ($this->arrayOfTokens[$i]->getType() == "INSTRUCTION") {
				if (($amountOfArguments = $this->getAmountOfRules($this->arrayOfTokens[$i])) < 0) {
					$this->throwException(21, "SYNTAX error analysis!", true);
				} else {
					if (!($this->checkArguments($this->arrayOfTokens, $i, $amountOfArguments))) {
						$this->throwException(21, "SYNTAX error analysis!", true);
					}
					$i += $amountOfArguments;
				}
			} else if ($this->arrayOfTokens[$i]->getType() == "NEWLINE" || $this->arrayOfTokens[$i]->getType() == "PROGRAM") {
				continue;
			} else {
				$this->throwException(21, "SYNTAX error analysis!", true);
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
	 * @param Token $inputToken
	 * @return integer
	 */
	private function getAmountOfRules($inputToken) {
		foreach ($this->syntaxRules as $key => $rules) {
			if ($inputToken->getContent() == $key) {
				return count($rules);
			}
		}
		return -1;
	}
}


/**
 * Class Stats
 */
class Stats extends Singleton {

	/**
	 * @var
	 */
	private $file;
	/**
	 * @var
	 */
	private $flags;
	/**
	 * @var
	 */
	private $first;

	/**
	 * @param $statsFile
	 * @param array $arrayOfFlags
	 * @param $first
	 */
	public function __construct($statsFile, $arrayOfFlags, $first) {
		$this->file = $statsFile;
		$this->flags = $arrayOfFlags;
		$this->first = $first;
	}

	/**
	 * @return mixed
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param mixed $file
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getFlags() {
		return $this->flags;
	}

	/**
	 * @param mixed $flags
	 */
	public function setFlags($flags) {
		$this->flags = $flags;
	}

	/**
	 * @return mixed
	 */
	public function getFirst() {
		return $this->first;
	}

	/**
	 * @param mixed $first
	 */
	public function setFirst($first) {
		$this->first = $first;
	}

	/**
	 * @param $amountOfLinesOfCode
	 * @param $amountOfComments
	 */
	public function saveToFile($amountOfLinesOfCode, $amountOfComments) {
		if ($this->getFlags()[0] && $this->getFlags()[1]) {
			$this->getFirst() == "L" ? $string = $amountOfLinesOfCode."\n".$amountOfComments."\n" : $string = $amountOfComments."\n".$amountOfLinesOfCode."\n";
		} else if ($this->getFlags()[0] && !($this->getFlags()[1])) {
			$string = $amountOfLinesOfCode;
		} else if (!($this->getFlags()[0]) && $this->getFlags()[1]) {
			$string = $amountOfComments;
		} else {
			$string = "";
		}
		if (file_put_contents($this->getFile(), $string) == FALSE) {
			$this->throwException(12, "File can not be saved: ".$this->getFile()."!", true);
		}
	}
}


/**
 * Class XML
 */
class XML extends Singleton {

	/**
	 * @var Token[]
	 */
	private $instructions;

	/**
	 * @var
	 */
	private $amountOfInstructions;

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
	 * @return string
	 */
	private function convertStringLiterals($string) {
		$stringArray = str_split($string);

		// todo wtf not working
		foreach ($stringArray as $key => $char) {
			if ($char == "&") {
				array_splice($stringArray, $key, 5, array('&', 'a', 'm', 'p', ';'));
			}
		}
		foreach ($stringArray as $key => $char) {
			if ($char == "<") {
				array_splice($stringArray, $key, 0, array('&', 'l', 't', ';'));
			}
		}
		foreach ($stringArray as $key => $char) {
			if ($char == ">") {
				array_splice($stringArray, $key, 0, array('&', 'g', 't', ';'));
			}
		}
		$newString = implode("", $stringArray);
		return $newString;
	}

	/**
	 * @param $amount
	 */
	private function setAmountOfInstructions($amount) {
		$this->amountOfInstructions = $amount;
	}

	/**
	 * @return integer
	 */
	public function getAmountOfInstructions() {
		return $this->amountOfInstructions;
	}

	/**
	 * @return mixed
	 */
	public function generateXml() {
		$instructions = $this->instructions;
		$xmlProgram = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"."<program></program>");
		$i = 0;
		$instructionIterator = 0;
		$argumentIterator = 1;
		while ($i < count($instructions)) {
			if ($instructions[$i]->getType() != "NEWLINE") {
				if ($instructions[$i]->getType() == "PROGRAM") {
					$xmlProgram->addAttribute('language', $instructions[0]->getContent());
				} else if ($instructions[$i]->getType() == "INSTRUCTION") {
					$instructionIterator++;
					$xmlInstruction = $xmlProgram->addChild('instruction');
					$xmlInstruction->addAttribute('order', $instructionIterator);
					$xmlInstruction->addAttribute('opcode', $instructions[$i]->getContent());
				} else {
					if (isset($xmlInstruction)) {
						$arg = "arg".$argumentIterator;
						$xmlArgument = $xmlInstruction->addChild($arg);
						$xmlArgument->addAttribute('type', $instructions[$i]->getType());
						// TODO Bug in this function, replaces & even though i dont want to replace it
						$xmlArgument[0] = $this->convertStringLiterals($instructions[$i]->getContent());
						$argumentIterator++;
					}
				}
				$i++;
			} else {
				while($i > 0) {
					array_shift($instructions);
					$i--;
				}
				array_shift($instructions);
				$argumentIterator = 1;
			}
		}
		$this->setAmountOfInstructions($instructionIterator);
		return $xmlProgram->asXML();
	}
}

$parser = new Parser();
$parser->parseArguments($argc, $argv);
$parser->printHelp();
$arrayOfLines = $parser->readFromStdinToInput();

$lex = new Lex($arrayOfLines);
$tokens = $lex->analyse();

$syntax = new Syntax($tokens);
$syntax->analyse();

$xml = new XML($tokens);
$xmlOutput = $xml->generateXml();

if ($parser->getSF()) {
	$stats = new Stats($parser->getStatsFile(), array($parser->getLF(), $parser->getCF()), $parser->getFirst());
	$stats->saveToFile($xml->getAmountOfInstructions(), $lex->getAmountOfComments());
}

echo $xmlOutput;

exit(0);
