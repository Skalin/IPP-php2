<?php
/**
 * Project: IPP-php2
 * User: Dominik Skala (xskala11)
 * Date: 11.2.18
 * Time: 16:44
 */

/*
 * Trida Singleton
 *
 * Slouzi jako globalni trida pro globalni funkce a promenne
 */
class Singleton {

	/**
	 * @var string Nazev analyzatoru
	 */
	private $fileName = "parse.php";

	/**
	 * Konstruktor tridy Singleton
	 */
	private function __construct() {

	}

	/**
	 * @return Singleton Instance tridy Singleton
	 */
	public static function Instance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new Singleton();
		}
		return $inst;
	}

	/**
	 * Funkce slouzici pro tisk chybovych zprav. Pokud je zavolana s parametrem $killable, ktery je true, funkce ukonci chovani skriptu
	 *
	 * @param integer $errorCode Ciselna hodnota chyboveho stavu
	 * @param string $errorText Obsah chyboveho hlaseni
	 * @param boolean $killable Parametr urcujici zda dojde k ukonceni skriptu - pokud je true, skript se ukonci, jinak ne
	 */

	public function throwException($errorCode, $errorText, $killable) {

		fwrite(STDERR, "ERROR: ".$errorText."\n");

		if ($killable) {
			fwrite(STDERR, "Please, consider looking for help, run script as: " . __DIR__ . "/" . $this->fileName . " --help\n");
			exit($errorCode);
		}
	}
}


/**
 * Trida Parser
 *
 * Provadi analyzu vstupnich dat, tedy jak argumentu tak vstupu ze STDIN pro dalsi analyzu zdrojoveho kodu
 */
class Parser extends Singleton {

	/**
	 * @var boolean Znacka urcujici zda byl zadan argument --help
	 */
	private $hF;
	/**
	 * @var boolean Znacka urcujici zda budou statistiky vubec ukladany
	 */
	private $sF;
	/**
	 * @var boolean Znacka urcujici zda budou statistiky pro pocet radku kodu ukladany
	 */
	private $lF;
	/**
	 * @var boolean Znacka urcujici zda budou statistiky pro pocet radku obsahujici komentar ukladany
	 */
	private $cF;
	/**
	 * @var string Soubor pro statistiky
	 */
	private $statsFile;
	/**
	 * @var string Znacka urcujici ktera ze statistik bude ulozena prvni: "L" pro pocet radku kodu nebo "C" pro pocet radku obsahujici komentar
	 */
	private $first;

	/**
	 * Konstruktor tridy Parser
	 */
	public function __construct() {
		$this->statsFile = "";
		$this->hF = false;
		$this->sF = false;
		$this->lF = false;
		$this->cF = false;
	}

	/**
	 * @return string Znacka urcujici ktera ze statistik bude ulozena prvni: "L" pro pocet radku kodu nebo "C" pro pocet radku obsahujici komentar
	 */
	public function getFirst() {
		return $this->first;
	}

	/**
	 * @param string $first Znacka urcujici ktera ze statistik bude ulozena prvni: "L" pro pocet radku kodu nebo "C" pro pocet radku obsahujici komentar
	 */
	public function setFirst($first) {
		$this->first = $first;
	}

	/**
	 * @return boolean Znacka urcujici zda byl zadan argument --help
	 */
	public function getHF() {
		return $this->hF;
	}

	/**
	 * @param boolean $hF Znacka urcujici zda byl zadan argument --help
	 */
	public function setHF($hF) {
		$this->hF = $hF;
	}

	/**
	 * @return boolean Znacka urcujici zda budou statistiky vubec ukladany
	 */
	public function getSF() {
		return $this->sF;
	}

	/**
	 * @param boolean $sF Znacka urcujici zda budou statistiky vubec ukladany
	 */
	public function setSF($sF) {
		$this->sF = $sF;
	}

	/**
	 * @return boolean Znacka urcujici zda budou statistiky pro pocet radku kodu ukladany
	 */
	public function getLF() {
		return $this->lF;
	}

	/**
	 * @param boolean $lF Znacka urcujici zda budou statistiky pro pocet radku kodu ukladany
	 */
	public function setLF($lF) {
		$this->lF = $lF;
	}

	/**
	 * @return boolean Znacka urcujici zda budou statistiky pro pocet radku obsahujici komentar ukladany
	 */
	public function getCF() {
		return $this->cF;
	}

	/**
	 * @param boolean $cF Znacka urcujici zda budou statistiky pro pocet radku obsahujici komentar ukladany
	 */
	public function setCF($cF) {
		$this->cF = $cF;
	}

	/**
	 * @return string Soubor pro ulozeni statistik
	 */
	public function getStatsFile() {
		return $this->statsFile;
	}

	/**
	 * @param string $statsFile Soubor pro ulozeni statistik
	 */
	public function setStatsFile($statsFile) {
		$this->statsFile = $statsFile;
	}

	/*
	 * Funkce cte data ze vstupu a uklada je do jednoho retezce, ktery je pote rozdelen podle radku a vracen jako pole radku
	 *
	 * @return string[] $input Vstupni text prekonvertovany do pole radku
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
	 * Tato funkce provadi tisk napovedy a ukonceni programu
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
	 * Funkce provede analyzu argumentu predanych pri spusteni, nastavi jednotlive znacky pro statistiky, napovedu a jine.
	 * Pokud dojde k chybe pri analyze (duplicitni argumenty, nevalidni kombinace), je zavolana funkce throwException a je vracen chybovy kod 10
	 *
	 * @param integer $argc Pocet argumentu predanych programu
	 * @param string[] $argv Pole argumentu predanych programu
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
 * Trida Token
 *
 * Tato trida definuje objekt typu Token, ktery je vyuzit pro syntaktickou analyzu, tokeny jsou tvoreny z instrukci, promennych, navesti a typovych hodnot
 * Kazdy token ma svuj typ $type a obsah $content, typem je myslena instrukce, promenna, navesti, typove urceni a konstanty
 * Obsah je slozen z obsahu textu, ktery byl pri tvorbe predan, u instrukci je to nazev instrukce, u promennych je to nazev ramce a promenne, atd.
 */
class Token {

	/**
	 * @var string Oznaceni typu tokenu
	 */
	private $type;
	/**
	 * @var string|null Obsah promenne, defaultne NULL
	 */
	private $content;

	/**
	 * Konstruktor tridy|objektu Token
	 *
	 * @param string $type Typ tokenu
	 * @param string|null $content Obsah tokenu
	 */
	public function __construct($type, $content = NULL) {
		$this->type = $type;
		$this->content = $content;
	}

	/**
	 * @return string Typ tokenu
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type Typ tokenu
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string|null Obsah tokenu
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string|null $content Obsah tokenu
	 */
	public function setContent($content) {
		$this->content = $content;
	}
}


/**
 * Trida Lex
 *
 * Provadi lexikalni analyzu zdrojoveho kodu, ktery byl ziskan ve tride Parser. Vytvari jednotlive tokeny, ktere jsou predany syntaktickemu analyzatoru
 */
class Lex extends Singleton {

	/**
	 * @var integer Pocet radku obsahujici komentar
	 */
	private $comments;

	/**
	 * @var string[] Pole radku ktere bude lexikalne analyzovano
	 */
	private $arrayOfLines;

	/**
	 * @var Token[] Pole tokenu pro syntaktickou analyzu
	 */
	private $tokenArray;

	/**
	 * @var string[] Pole klicovych slov pro instrukce
	 */
	private $arrayOfInstructions = array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS",
		"POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STRI2INT",
		"READ", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP", "JUMPIFEQ",
		"JUMPIFNEQ", "DPRINT", "BREAK");

	/**
	 * Konstruktor tridy Lex
	 *
	 * Provadi inicializaci tridnich promennych arrayOfLines, tokenArray a comments
	 *
	 * @param string[] $arrayOfLines Pole radku ze standardniho vstupu
	 */
	public function __construct($arrayOfLines) {
		$this->arrayOfLines = $arrayOfLines;
		$this->tokenArray = array();
		$this->comments = 0;
	}

	/**
	 * @return integer Pocet radku obsahujici komentar
	 */
	public function getAmountOfComments() {
		return $this->comments;
	}

	/**
	 * @param integer Pocet radku obsahujici komentar
	 */
	private function setAmountOfComments($comments) {
		$this->comments = $comments;
	}

	/**
	 * Funkce provede kontrolu a kraceni pole o prazdne radky
	 *
	 * @param string[] $array Pole ktere bude orezano
	 * @return array Pole orezane o prazdne radky
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
	 * Funkce provede kontrolu zda se ve zdrojovem kodu nenachazi ihned za zdrojovym kodem komentar (bez mezery), usnadni nam lexikalni analyzu komentaru
	 *
	 * @param string[] $wordArray Radek transformovany do pole, ve kterem je komentar napsan primo za zdrojovym kodem, bez mezery
	 * @return string[] Pole slov v radku s komentari oddelenymi od zdrojoveho kodu
	 */
	private function splitComments($wordArray) {
		$newArray = array();
		foreach ($wordArray as $key => $word) {
			if (preg_match('/#(.*)/', $word)) {
				array_push($newArray, substr($word,0, strpos($word, "#"))); // instrukce
				array_push($newArray, substr($word, strpos($word, "#"))); // komentar
				break;
			} else {
				array_push($newArray, $word);
			}
		}
		$newArray = $this->cleanArray($newArray);
		return $newArray;
	}

	/**
	 * Funkce provede lexikalni analyzu vsech radku kodu, pole radku musi zacinat retezcem: .IPPcode18 (v jakekoliv velikosti vsech pismen), nasledne dojde k rozdeleni vsech radku podle mezer a kazdy "retezec" se zkontroluje vuci lexikalnim pravidlum.
	 * Pokud dojde k lexikalni chybe, je zavolana funkce throwException s navratovym kodem 21
	 *
	 * @return Token[] Pole tokenu, ktere bylo zanalizovano
	 */
	public function analyse() {
		if (count($this->arrayOfLines) > 0) {
			// zdrojovy kod musi zacinat retezcem: .IPPcode18
			if (substr(strtoupper($this->arrayOfLines[0]), 0, 10) == ".IPPCODE18") {
				$token = new Token("PROGRAM", "IPPcode18");
				array_push($this->tokenArray, $token);
				array_shift($this->arrayOfLines);
			} else {
				$this->throwException(21, "Given token: ". $this->arrayOfLines[0].", expected language specification: .IPPcode18", false);
				$this->throwException(21, "LEX error analysis!",true);
			}
			foreach ($this->arrayOfLines as $line) {
				$row = preg_replace('/\s+/', ' ',$line);
				$rowArray = explode(" ", $row);
				$rowArray = $this->splitComments($rowArray);
				$previousToken = "";
				for ($i = 0; $i < count($rowArray); $i++) {
					switch($rowArray[$i]) {
						// instrukce
						case in_array(strtoupper($rowArray[$i]), $this->arrayOfInstructions):
							if (preg_match('/(INSTRUCTION\.)(CALL|LABEL|JUMP|JUMPIFEQ|JUMPIFNEQ)/', $previousToken) == true) {
								// kontrola pro navesti se stejnym jmenem jako nazev instrukce
								if (preg_match('/^[%|_|\-|\$|&|\*|A-z]{1}[%|_|\-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) == true) {
									$token = new Token("LABEL", $rowArray[$i]);
								} else {
									$this->throwException(21, "Token: ". $rowArray[$i], false);
									$this->throwException(21, "LEX error analysis!", true);
								}
							} else {
								$token = new Token("INSTRUCTION", strtoupper($rowArray[$i]));
							}
							array_push($this->tokenArray, $token);
							$previousToken = $token->getType().".".$token->getContent();
							break;
						// promenne
						case (preg_match('/^(LF|TF|GF)@[%|_|\-|\$|&|\*|A-z]{1}[%|_|\-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) ? true : false):
							$token = new Token("VAR", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						// string
						case (preg_match('/^(string)@[\S]*$/', $rowArray[$i]) ? true : false):
							$token = new Token("CONSTANT", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						// int
						case(preg_match('/^(int)@[\-|\+|0-9]+[0-9]*$/', $rowArray[$i]) ? true : false):
							$token = new Token("CONSTANT", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						// bool
						case(preg_match('/^(bool)@(true|false)$/', $rowArray[$i]) ? true : false):
							$token = new Token("CONSTANT", $rowArray[$i]);
							array_push($this->tokenArray, $token);
							break;
						// komentare
						case (preg_match('/#(.*)/', $rowArray[$i]) ? true : false):
							// comments
							$this->setAmountOfComments($this->getAmountOfComments()+1);
							break 2;
						// navesti a typove oznaceni
						case (preg_match('/^[%|_|\-|\$|&|\*|A-z]{1}[%|_|\-|\$|&|\*|A-z|0-9]+$/', $rowArray[$i]) ? true : false):
							// typ
							if (preg_match('/^(int|string|bool)$/', $rowArray[$i]) == true) {
								$token = new Token("TYPE", $rowArray[$i]);
							// navesti
							} else {
								$token = new Token("LABEL", $rowArray[$i]);
							}
							array_push($this->tokenArray, $token);
							break;
						default:
							$this->throwException(21, "Unrecognizable token: ". $rowArray[$i], false);
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
 * Trida Syntax
 *
 * Provadi syntaktickou analyzu zdrojoveho kodu, ktery je slozen z tokenu, ktere byly vytvoreny v tride Lex.
 */
class Syntax extends Singleton {

	/**
	 * @var string[][] Pole syntaktickych pravidel pro vsechny instrukce z jazyka IPPcode18
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
	 * @var string[] Zastupne pole pro "SYMB" znacku, pro usetreni prace pri prochazeni pole argumentu instrukci
	 */
	private $symbs = array("VAR", "CONSTANT");

	/**
	 * @var Token[] Pole tokenu
	 */
	private $arrayOfTokens;

	/**
	 * Konstruktor tridy Syntax
	 *
	 * @param Token[] $tokenArray Pole tokenu
	 */
	public function __construct($tokenArray) {
		$this->arrayOfTokens = $tokenArray;
	}

	/**
	 * Funkce provede orezani pole od duplicitnich tokenu typu "NEWLINE", ktere byly vytvoreny u prazdnych radku. Jsou mazany pouze dva a vice "NEWLINE" tokenu za sebou, pricemz zustane pouze jeden
	 *
	 * @param Token[] $arrayOfTokens Pole tokenu
	 * @return Token[] Pole orezane o duplicitni "NEWLINE" tokeny
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
	 * Funkce prochazi zdrojovy kod a scita pocet jednotlivych tokenu dokud nenarazi na "NEWLINE" token, ktery urcuje konec radku.
	 *
	 * @param Token[] $tokenArray Pole tokenu
	 * @param integer $start Pocatecni index v poli tokenu
	 * @return integer Pocet argumentu ktere byly nalezeny ve zdrojovem kodu od $start po "NEWLINE" token.
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
	 * Funkce provadi overeni spravnosti argumentu u zadane instrukce na indexu $start v poli $tokenArray, toto overeni provadi pro argumenty ktere jsou az po prvek $start+$amount
	 * Nejprve je zkontrolovan pocet argumentu a ocekavany pocet argumentu instrukce, nasledne se projde cele pole od pocatecniho indexu $start po $start+$amount.
	 * Pokud jsou argumenty v souladu s pravidly z tridni promenne pole $syntaxRules, analyza probehla v poradku, jinak se se navrati false
	 * Pokud dojde k chybe pri analyze, dojde k zavolani funkce throwException, ktera ale neukonci program a nasledne se navrati chybovy stav
	 *
	 * @param Token[] $tokenArray Pole tokenu
	 * @param integer $start Pocatecni index na kterem je umistena instrukce
	 * @param integer $amount Pocet prvku, ktere budou pro danou instrukci kontorlovany
	 * @return boolean Pokud analyza probehne v poradku, navrati se true, jinak false
	 */
	private function checkArguments($tokenArray, $start, $amount) {
		$amountOfArguments = $this->getAmountOfArguments($tokenArray, $start);
		if ($amountOfArguments != $amount) {
			$this->throwException(21, "Wrong amount of arguments, expected: ". $amount.", given: ".$amountOfArguments, false);
			return false;
		} else {
			$rules = $this->getRules($tokenArray[$start]);
			for ($i = 0; $i <= ($amount - 1); $i++) {
				if ($rules[$i] == "VAR" || $rules[$i] == "LABEL" || $rules[$i] == "TYPE") {
					if ($rules[$i] == $tokenArray[$start + $i + 1]->getType()) {
					} else {
						$this->throwException(21, "Unexpected type of token, expected: ". $rules[$i].", given: ".$tokenArray[$start + $i + 1]->getType(), false);
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
					$this->throwException(21, "Unexpected type of token: ".$tokenArray[$start + $i + 1]->getType(), false);
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Funkce provadi syntaktickou analyzu kodu, nejprve zkontroluje ze se jedna o korektni instrukci nebo pocatecni stav, pripadne NEWLINE ktery preskoci
	 * Pokud se jedna o INSTRUKCI, zkontroluje pocet pravidel pro danou instrukci, po uspesne kontrole instrukce provede samotnou syntaktickou analyzu kontrolou argumentu instrukci
	 * Pokud dojde k chybe pri overovani podminek, zavola se funkce throwException, ktera ukonci program s navratovym kodem 21
	 *
	 */
	public function analyse() {
		$this->arrayOfTokens = $this->cleanDuplicateNewLines($this->arrayOfTokens);
		for ($i = 0; $i < count($this->arrayOfTokens); $i++) {
			if ($this->arrayOfTokens[$i]->getType() == "INSTRUCTION") { // pokud je prvni token INSTRUKCE, provedeme analyzu
				if (($amountOfArguments = $this->getAmountOfRules($this->arrayOfTokens[$i])) < 0) { // zkontrolujeme pocet pravidel (overeni validity instrukce pro jazyk)
					$this->throwException(21, "Given amount of arguments: ".$amountOfArguments, false);
					$this->throwException(21, "SYNTAX error analysis!", true);
				} else {
					if (!($this->checkArguments($this->arrayOfTokens, $i, $amountOfArguments))) { // overime validity argumentu pro danou instrukci (samotna syntakticka analyza)
						$this->throwException(21, "Wrong amount of arguments for instruction: ".$this->arrayOfTokens[$i]->getContent(), false);
						$this->throwException(21, "SYNTAX error analysis!", true);
					}
					$i += $amountOfArguments;
				}
			} else if ($this->arrayOfTokens[$i]->getType() == "NEWLINE" || $this->arrayOfTokens[$i]->getType() == "PROGRAM") { // pokud se jedna o novy radek nebo uvodni radek s urcenim programu, provedeme dalsi iteraci
				continue;
			} else { // jinak vratime chybovy stav
				$this->throwException(21, "SYNTAX error analysis!", true);
			}
		}
	}

	/**
	 * Funkce ziska pole ocekavanych argumentu pro zadanou instrukci
	 *
	 * @param Token $inputToken Token obsahujici instrukci, pro kterou budou hledana pravidla
	 * @return string[]|boolean Pole pravidel pro danou instrukci, nebo false pokud neexistuje platna instrukce
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
	 * Funkce vraci pocet pravidel, ktere jsou pro dany token ocekavany
	 *
	 * @param Token $inputToken Token obsahujici instrukci, pro kterou bude hledan pocet pravidel
	 * @return integer Pocet pravidel, -1 pro neexistujici pravidlo, jinak vrati korektni pocet pravidel z pole tridni promenne $syntaxRules
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
 * Trida Stats
 *
 * Ze znacek z Parseru a ze statistik ktere jsou ziskany pri analyze vytvari soubor se statistikami
 */
class Stats extends Singleton {

	/**
	 * @var string Nazev souboru statistik
	 */
	private $file;
	/**
	 * @var boolean[] Pole znacek urcujici povolovani statistik pro jednotlive parametry
	 */
	private $flags;
	/**
	 * @var string Parametr urcuje prioritu vypisu statistik
	 */
	private $first;

	/**
	 * Konstruktor tridy Stats
	 *
	 * @param string $statsFile Soubor pro statistiky
	 * @param boolean[] $arrayOfFlags Pole znacek, urcujici ktera ze statistik je povolena
	 * @param string $first Parametr obsahuje "L" nebo "C", urcuje ktera ze statistik se do souboru ulozi prvni - L: pocet radku s kodem, C: pocet radku obsahujici komentar
	 */
	public function __construct($statsFile, $arrayOfFlags, $first) {
		$this->file = $statsFile;
		$this->flags = $arrayOfFlags;
		$this->first = $first;
	}

	/**
	 * @return string Soubor pro statistiky
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @param string $file Soubor pro statistiky
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * @return boolean[] Pole znacek ve tvaru L|C, urcuje ktera ze statistik bude vytvorena
	 */
	public function getFlags() {
		return $this->flags;
	}

	/**
	 * @param string[] $flags Pole znacek ve tvaru L|C, urcuje ktera ze statistik bude vytvorena
	 */
	public function setFlags($flags) {
		$this->flags = $flags;
	}

	/**
	 * @return string Hodnota "L" nebo "C" urcujici ktery z parametru bude v souboru vytisten prvni
	 */
	public function getFirst() {
		return $this->first;
	}

	/**
	 * @param string $first Hodnota "L" nebo "C" urcujici ktery z parametru bude v souboru vytisten prvni
	 */
	public function setFirst($first) {
		$this->first = $first;
	}

	/**
	 * @param integer $amountOfLinesOfCode Pocet radku kodu
	 * @param integer $amountOfComments Pocet radku na kterych se vyskytoval komentar
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
 * Trida XML
 *
 * Slouzi k tvorbe XML vystupu, ktery je tvoren z tokenu, ktere byly predany ze syntaktickeho analyzatoru
 */
class XML extends Singleton {

	/**
	 * @var Token[] Pole tokenu
	 */
	private $instructions;

	/**
	 * @var integer Pocet radku s instrukcemi pro statistiky
	 */
	private $amountOfInstructions;

	/**
	 * Konstruktor tridy XML
	 *
	 * @param $instructions[] Pole instrukci
	 */
	public function __construct($instructions) {
		$this->instructions = $instructions;
	}

	/**
	 * Funkce provede konverzi znaku &, > a < ve stringu, ktery se ulozi do XML za ucelem maximalni kompatibility
	 *
	 * @param string $string Upravovany retezec
	 * @return string Upraveny retezec
	 */
	private function convertStringLiterals($string) {
		$newString = $string;
		str_replace("&", "&amp;", $newString);
		str_replace("<", "&lt;", $newString);
		str_replace(">", "&gt;", $newString);
		return $newString;
	}

	/**
	 * @param integer $amount Pocet instrukci
	 */
	private function setAmountOfInstructions($amount) {
		$this->amountOfInstructions = $amount;
	}

	/**
	 * @return integer Pocet instrukci
	 */
	public function getAmountOfInstructions() {
		return $this->amountOfInstructions;
	}

	/**
	 * Funkce z třídní proměnné $instructions ziska soupis instrukci, ktere byly zvalidovany a vytvori z nich XML Element
	 *
	 * @return SimpleXMLElement XML Element obsahujici jednotlive operace s argumenty po analyze zdrojoveho kodu
	 */
	public function generateXml() {
		$instructions = $this->instructions;
		$xmlProgram = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"."<program></program>"); // hlavicka programu
		$i = 0;
		$instructionIterator = 0;
		$argumentIterator = 1;
		while ($i < count($instructions)) {
			if ($instructions[$i]->getType() != "NEWLINE") {
				if ($instructions[$i]->getType() == "PROGRAM") {
					$xmlProgram->addAttribute('language', $instructions[0]->getContent()); // element urcujici jazyk kodu
				} else if ($instructions[$i]->getType() == "INSTRUCTION") {
					$instructionIterator++;
					$xmlInstruction = $xmlProgram->addChild('instruction'); // element urcujici instrukce
					$xmlInstruction->addAttribute('order', $instructionIterator);
					$xmlInstruction->addAttribute('opcode', $instructions[$i]->getContent());
				} else {
					if (isset($xmlInstruction)) {
						$arg = "arg".$argumentIterator;
						$xmlArgument = $xmlInstruction->addChild($arg); // tvorba deti rodice $xmlInstruction
						$xmlArgument->addAttribute('type', $instructions[$i]->getType());
						$xmlArgument[0] = $this->convertStringLiterals($instructions[$i]->getContent());
						$argumentIterator++;
					}
				}
				$i++;
			} else {
				// orezavani pole od jiz vygenerovanych xml instrukci a argumentu
				while($i > 0) {
					array_shift($instructions);
					$i--;
				}
				array_shift($instructions);
				$argumentIterator = 1;
			}
		}
		$this->setAmountOfInstructions($instructionIterator);
		return $xmlProgram;
	}
}

// analyza argumentu
$parser = new Parser();
$parser->parseArguments($argc, $argv);
$parser->printHelp();
$arrayOfLines = $parser->readFromStdinToInput();

// lexikalni analyza vstupu
$lex = new Lex($arrayOfLines);
$tokens = $lex->analyse();

// syntakticka analyza
$syntax = new Syntax($tokens);
$syntax->analyse();

// generovani xml vystupu
$xml = new XML($tokens);
$xmlOutput = $xml->generateXml();

// tvorba statistik
if ($parser->getSF()) {
	$stats = new Stats($parser->getStatsFile(), array($parser->getLF(), $parser->getCF()), $parser->getFirst());
	$stats->saveToFile($xml->getAmountOfInstructions(), $lex->getAmountOfComments());
}

// formatovani xml vystupu
$dom = dom_import_simplexml($xmlOutput)->ownerDocument;
$dom->formatOutput = true;
echo $dom->saveXml();

exit(0);
