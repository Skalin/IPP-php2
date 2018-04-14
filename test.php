<?php
/**
 * Project: IPP
 * User: Dominik Skala (xskala11)
 * Date: 20.2.18
 * Time: 16:11
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
		private $fileName = "test.php";

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
	 * Provadi analyzu vstupnich dat, tedy zejmena argumentu
    */
class Parser extends Singleton {

	/*
	 * @var integer Pocet argumentu zadanych na vstupu skriptu
	 */
	private $argumentCount;

	/*
	 * @var string[] Pole argumentu programu
	 */
	private $arguments;

	/**
	 * @var boolean Znacka urcujici zda byl zadan argument --help
	 */
	private $hF;

	/*
	 * @var string slozka ve ktere jsou testy ulozeny
	 */
	private $dirPath;

	/*
	 * @var boolean znacka urcujici zda bude provadeno rekurzivni prohledavani
	 */
	private $rF;

	/*
	 * @var boolean znacka urcujici zda byl zadan --interpret parametr
	 */
	private $iF;

	/*
	 * @var boolean znacka urcujici zda byl zadan parametr --directory
	 */
	private $dF;

	/*
	 * @var boolean znacka urcujici zda byl zadan --parse parametr
	 */
	private $pF;

	/*
	 * @var string cesta k lexikalnimu analyzatoru jazyka IPPcode18
	 */
	private $parsePath;

	/*
	 * @var string cesta k interpretu jazyka IPPcode18
	 */
	private $interpretPath;

	/**
	 * Konstruktor tridy Parser
	 *
	 * @param int $argc pocet argumentu
	 * @param string[] $argv pole argumentu
	 */
	public function __construct($argc, $argv) {
		$this->argumentCount = $argc;
		$this->arguments = $argv;
		$this->hF = false;
		$this->dirPath = "./";
		$this->dF = false;
		$this->rF = false;
		$this->pF = false;
		$this->parsePath = "./parse.php";
		$this->iF = false;
		$this->interpretPath = "./interpret.py";
	}

	/**
	 * Tato funkce provadi tisk napovedy a ukonceni programu
	 *
	 */
	public function printHelp() {
		if ($this->isHF()) {
			echo "test.php - lexikalni a syntakticky analyzator v jazyce PHP 5.6\n";
			echo "Autor: Dominik Skala (xskala11)\n";
			echo "Predmet: IPP 2018\n";
			echo "Automatická testovací aplikace pro IPP syntaktický a lexikální analyzátor a interpret pro jazyk IPPcode18.\n";
			echo "Implementovana rozsireni: STATS\n\n";
			echo "Argumenty:\n";
			exit(0);
		}
	}
	/**
	 * Getter pro tridni promennou hF
	 * @return bool
	 */
	public function isHF() {
		return $this->hF;
	}

	/**
	 * Setter pro tridni promennou $hF
	 * @param bool $hF
	 */
	public function setHF($hF) {
		$this->hF = $hF;
	}

	/**
	 * Getter pro tridni promennou $dirPath
	 * @return string
	 */
	public function getDirPath() {
		return $this->dirPath;
	}

	/**]
	 * Setter pro tridni promennou $dirPath
	 * @param string $dirPath
	 */
	public function setDirPath($dirPath) {
		$this->dirPath = $dirPath;
	}

	/**
	 * Getter pro tridni promennou iF
	 * @return bool
	 */
	public function isIF() {
		return $this->iF;
	}

	/**
	 * Setter pro tridni promennou $iF
	 * @param bool $iF
	 */
	public function setIF($iF) {
		$this->iF = $iF;
	}

	/**
	 * Getter pro tridni promennou pF
	 * @return bool
	 */
	public function isPF() {
		return $this->pF;
	}

	/**
	 * Setter pro tridni promennou $pF
	 * @param bool $pF
	 */
	public function setPF($pF) {
		$this->pF = $pF;
	}

	/**
	 * Getter pro tridni promennou dF
	 * @return bool
	 */
	public function isDF() {
		return $this->dF;
	}

	/**
	 * Setter pro tridni promennou $dF
	 * @param bool $dF
	 */
	public function setDF($dF) {
		$this->dF = $dF;
	}

	/**
	 * Getter pro tridni promennou rF
	 * @return bool
	 */
	public function isRF() {
		return $this->rF;
	}

	/**
	 * Setter pro tridni promennou $rF
	 * @param bool $rF
	 */
	public function setRF($rF) {
		$this->rF = $rF;
	}

	/**
	 * Getter pro tridni promennou $parsePath
	 * @return string
	 */
	public function getParsePath() {
		return $this->parsePath;
	}

	/**
	 * Setter pro tridni promennou $parsePath
	 * @param string $parsePath
	 */
	public function setParsePath($parsePath) {
		$this->parsePath = $parsePath;
	}

	/**
	 * Getter pro tridni promennou $interpretPath
	 * @return string interpretPath
	 */
	public function getInterpretPath() {
		return $this->interpretPath;
	}

	/**
	 * Setter pro tridni promennou $interpretPath
	 * @param string $interpretPath cesta k interpretu
	 */
	public function setInterpretPath($interpretPath) {
		$this->interpretPath = $interpretPath;
	}

	/**
	 * Getter pro tridni promennou $argumentCount
	 * @return integer argumentCount
	 */
	public function getArgumentCount() {
		return $this->argumentCount;
	}

	/**
	 * Getter pro tridni promennou $arguments
	 * @return string[] arguments
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * Funkce provede analyzu argumentu predanych pri spusteni, nastavi jednotlive znacky pro statistiky, napovedu a jine.
	 * Pokud dojde k chybe pri analyze (duplicitni argumenty, nevalidni kombinace), je zavolana funkce throwException a je vracen chybovy kod 10
	 */
	public function parseArguments() {
		if ($this->argumentCount != 1) {
			for ($i = 1; $i < $this->argumentCount; $i++) {
				if (preg_match("/--help/", $this->arguments[$i]) == 1 && $this->isHF() != true) {
					$this->setHF(true);
				} else if (preg_match("/--recursive/", $this->arguments[$i]) == 1 && $this->isRF() != true) {
					$this->setRF(true);
				} else if (preg_match("/--directory=.*/", $this->arguments[$i]) == 1 && $this->getDirPath() == "./") {
					$this->setDF(true);
					$this->setDirPath(substr($this->arguments[$i], 12));
				} else if (preg_match("/--parse-script=.*/", $this->arguments[$i]) == 1 && $this->getParsePath() == "parse.php") {
					$this->setPF(true);
					$this->setParsePath(substr($this->arguments[$i], 15));
				} else if (preg_match("/--int-script=.*/", $this->arguments[$i]) == 1 && $this->getInterpretPath() == "interpret.py") {
					$this->setIF(true);
					$this->setInterpretPath(substr($this->arguments[$i], 13));
				} else {
					$this->throwException(10, "Wrong usage of arguments!", true);
				}
			}
			if ((($this->isPF() || $this->isIF() || $this->isDF() || $this->isRF()) && $this->isHF()) == true) {
				$this->throwException(10, "Wrong usage of arguments!", true);
			}
		}
	}
}
/*
 * Trida Test
 *
 * Tato trida definuje jednotlive testy a jejich navratove hodnoty, vystupy, atd.
 */
class Test extends Singleton {

	/*
	 * @var string nazev testu
	 */
	private $name;

	/*
	 * @var integer ocekavany navratovy kod testu
	 */
	private $erc;

	/*
	 * @var string Vysledek testu
	 */
	private $testStatus;

	/*
	 * Cesta k parseru
	 */
	private $parser;
	private $interpret;
	private $resultCode;

	/*
	 * Konstruktor tridy Test
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Getter pro tridni promennou $name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Setter pro tridni promennou $name
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Getter pro tridni promennou $erc
	 * @return int ocekavany navratovy kod
	 */
	public function getErc() {
		return $this->erc;
	}

	/**
	 * Setter pro tridni promennou $erc
	 * @param string $erc
	 */
	public function setErc($erc) {
		$this->erc = intval($erc);
	}

	/**
	 * @return mixed
	 */
	public function getTestStatus() {
		return $this->testStatus;
	}

	/**
	 * @param string $testStatus
	 */
	public function setTestStatus($testStatus) {
		$this->testStatus = $testStatus;
	}

	/**
	 * Setter pro tridni promennou $resultCode
	 * @return string cesta k parseru
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * Setter pro tridni promennou $parser
	 * @param string $parser cesta k parseru
	 */
	public function setParser($parser) {
		$this->parser = $parser;
	}

	/**
	 * Getter pro tridni promennou $interpret
	 * @return string cesta k interpretu
	 */
	public function getInterpret() {
		return $this->interpret;
	}

	/**
	 * Setter pro tridni promennou $interpret
	 * @param string $interpret cesta k interpretu
	 */
	public function setInterpret($interpret) {
		$this->interpret = $interpret;
	}

	/*
	 * Setter pro tridni promennou $resultCode
	 * @param int $code navratovy kod testu
	 */
	public function setResultCode($code) {
		$this->resultCode = $code;
	}

	/*
	 * Getter pro tridni promennou $resultCode
	 * @return int navratovy kod testu
	 */
	public function getResultCode() {
		return $this->resultCode;
	}

	/*
	 * Funkce kontroluje jestli test.in existuje
	 *
	 * @return bool true pokud soubor exsistuje, jinak false
	 */
	public function inFileExists() {
		if ((file_exists($this->getName().".in") && (is_dir($this->getName().".in"))) || (!file_exists($this->getName().".in"))) {
			return false;
		}
		return true;
	}

	/*
	 * Funkce kontroluje jestli test.out existuje
	 *
	 * @return bool true pokud soubor exsistuje, jinak false
	 */
	public function outFileExists() {
		if ((file_exists($this->getName().".out") && (is_dir($this->getName().".out"))) || (!file_exists($this->getName().".out"))) {
			return false;
		}
		return true;
	}

	/*
	 * Funkce kontroluje jestli test.rc existuje
	 *
	 * @return bool true pokud soubor exsistuje, jinak false
	 */
	public function rcFileExists() {
		if ((file_exists($this->getName().".rc") && (is_dir($this->getName().".rc"))) || (!file_exists($this->getName().".rc"))) {
			return false;
		}
		return true;
	}

	/*
	 * Funkce generuje soubor test.in, s vychozim obsahem "\0"
	 *
	 * Pokud zapis selhal z jakehokoliv duvodu, vraci chybovy kod 12
	 */
	public function generateInFile() {
		if ((file_put_contents($this->getName().".in", "\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	/*
		 * Funkce generuje soubor test.out, s vychozim obsahem "\0"
		 *
		 * Pokud zapis selhal z jakehokoliv duvodu, vraci chybovy kod 12
		 */
	public function generateOutFile() {
		if ((file_put_contents($this->getName().".out", "\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	/*
	 * Funkce generuje soubor test.rc, s vychozim obsahem "0"
	 *
	 * Pokud zapis selhal z jakehokoliv duvodu, vraci chybovy kod 12
	 */
	public function generateRcFile() {
		if ((file_put_contents($this->getName().".rc", "0\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	/*
	 * Funkce nacte obsah souboru test.rc
	 *
	 * Pokud soubor neexistuje vraci chybovy kod 12
	 *
	 * @return string $read precteny obsah souboru
	 */
	public function loadErcFromFile() {
		if (($read = file_get_contents($this->getName().".rc")) == false) {
			$this->throwException(12, "ERROR reading file", true);
		}
		return $read;
	}

	/*
	 * Funkce porovna navratovy kod s ocekavanym navratovym kodem a vraci true pokud se shoduji
	 *
	 * @param int $returnCode navratovy kod z analyzy / interpretace kodu
	 * @return bool true pokud se shoduji, jinak false
	 */
	public function compareReturnCode($returnCode) {
		return ($this->getErc() == $returnCode ? true : false);
	}
}

/*
 * Trida TestBehavior
 *
 * Spravuje chovani jednotlivych testu jako logickeho celku
 */
class TestBehavior extends Singleton {

	/*
	 * @var Test[] Pole testu
	 */
	private $testArray;

	/*
	 * @var string Cesta k lexikalnimu analyzatoru
	 */
	private $parser;

	/*
	 * @var string Cesta k interpretu jazyka IPPcode18
	 */
	private $interpret;

	/*
	 * Pole testovych vysledku, kazdy test nabyva pri inicializaci hodnoty "IGNORE", uspesny test nabyva hodnoty "SUCCESS" a neuspesny "FAIL"
	 */
	private $testResults = array("IGNORE", "SUCCESS", "FAIL");

	/**
	 * TestBehavior konstruktor.
	 *
	 * @param Test[] $testArray Pole testu
	 * @param string $parser cesta ke skriptu analyzatoru
	 * @param string $interpret cesta ke skriptu interpretu
	 */
	public function __construct(array $testArray, $parser, $interpret) {
		$this->testArray = $testArray;
		$this->parser = $parser;
		$this->interpret = $interpret;
	}

	/**
	 * Getter pro tridni promennou $testArray
	 * @return array
	 */
	private function getTestArray() {
		return $this->testArray;
	}

	/**
	 * Getter pro tridni promennou $parser
	 * @return string cesta k souboru analyzatoru
	 */
	private function getParser() {
		return $this->parser;
	}

	/**
	 * Getter pro tridni promennou $interpret
	 * @return string cesta k souboru interpretu
	 */
	private function getInterpret() {
		return $this->interpret;
	}

	/*
	 * FFunkce porovnava ocekavany vstup a realny vystup programu
	 *
	 * @param int $expectedOutput ocekavany vstup
	 * @param int $givenOutput ziskany vstup
	 * @return bool true pokud se shoduji, jinak false
	 */
	private function compareOutput($expectedOutput, $givenOutput) {
		if (strcmp($expectedOutput, $givenOutput) == 0) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Funkce vraci true pokud existuje soubor (skript)
	 *
	 * @param string $file cesta k souboru
	 * @return bool true pokud soubor existuje, jinak false
	 */
	private function programExists($file) {
		return (is_file($file) ? true : false);
	}

	/**
	 * Funkce provede chovani testu jako logickeho celku, kazdy test ma jasne a stejne chovani ktere se da oznacit jako "Behavior"
	 *
	 * @return Test[] vysledne pole testu
	 */
	public function testBehavior() {
		foreach ($this->getTestArray() as $test) {

			$test->setTestStatus($this->testResults[0]);

			if (!$test->inFileExists()) {
				$test->generateInFile();
			}
			if (!$test->outFileExists()) {
				$test->generateOutFile();
			}
			if (!$test->rcFileExists()) {
				$test->generateRcFile();
			}

			$test->setErc($test->loadErcFromFile());


			$returnVal = 0;
			if ($this->programExists($this->getParser())) {
				exec('php -f '.$this->getParser().' < '.$test->getName().".src", $output, $returnVal);
			} else {
				$this->throwException(11, "Parser does not exist", true);
			}

			if ($returnVal != 0) {
				if (!$test->compareReturnCode($returnVal)) {
					$test->setResultCode($returnVal);
					$test->setTestStatus($this->testResults[2]);
					continue;
				}
			}

			$xml = implode($output, "\n");

			if ($this->programExists($this->getInterpret())) {
				exec('python3 '.$this->getInterpret().' < '.$test->getName().".in", $output, $returnVal);
			} else {
				$this->throwException(11, "Interpret does not exist", true);
			}
			if ($returnVal != 0) {
				if (!$test->compareReturnCode($returnVal)) {
					$test->setResultCode($returnVal);
					$test->setTestStatus($this->testResults[2]);
					continue;
				}
			}

			if ($this->compareOutput(file_get_contents($test->getName()."out"), $output)) {
				$test->setTestStatus($this->testResults[1]);
			} else {
				$test->setTestStatus($this->testResults[2]);
				continue;
			}

		}
		return $this->getTestArray();
	}
}

/*
 * Trida TestDirectory
 *
 * Trida generujici pole testu z testovaci slozky
 */
class TestDirectory extends Singleton {

	/*
	 * @var bool znacka urcujici zda dochazi k rekurzivnimu prohledavani nebo ne
	 */
	private $recursive;

	/*
	 * @var Test[] pole testu, ktere se vygeneruje
	 */
	private $arrayOfTests = array();

	/*
	 * Konstruktor tridy TestDirectory
	 */
	public function __construct($recursive) {
		$this->recursive = $recursive;
	}

	/*
	 * Funkce urcuje zda dojde k rekurzivnimu prohledavani adresare s testy
	 *
	 * @returns boolean true pokud dojde k prohledavani, false pokud ne
	 */
	private function isRecursive() {
		return $this->recursive ? true : false;
	}

	/*
	 * Funkce vyhleda v dane slozce veskere testy a vytvori z nich objekty typu Test, ktere vlozi do pole testu a to navrati
	 * @param string $dir cesta ve ktere budou testy hledany
	 * @return Test[] pole testu
	 */
	public function createTests($dir) {
		if (($files = scandir($dir)) == false) {
			$this->throwException(10,"Directory is not a directory", true);
		} else {
			foreach ($files as $fd) {
				if ($fd == "." || $fd == "..") {
					continue;
				}
				if (is_dir($dir."/".$fd)) {
					if ($this->isRecursive()) {
						$this->createTests($dir."/".$fd);
					} else {
						continue;
					}
				} else {
					if (preg_match('/.+\.src$/', $fd) == true) {
						$test = new Test($dir."/".substr($fd, 0, strlen($fd)-4));
						array_push($this->arrayOfTests, $test);
					} else {
						continue;
					}
				}
			}
		}
		return $this->arrayOfTests;
	}
}

/*
 * Trida HtmlGenerator
 *
 * Provadi tvorbu a generovani vystupniho HTML, ktere je ucelne pro vysledek testu
 */
class HtmlGenerator extends Singleton {

	/*
	 * var Test[] $testArray
	 */
	private $testArray;
	private $directory;
	private $parser;
	private $interpret;
	private $arguments;

	/*
	 * Konstruktor tridy HtmlGenerator
	 */
	public function __construct($testArray, $directory, $parser, $interpret, $arguments) {
		$this->testArray = $testArray;
		$this->directory = $directory;
		$this->parser = $parser;
		$this->interpret = $interpret;
		$this->arguments = $arguments;
	}

	/**
	 * Getter pro ziskani tridni promenne $directory
	 * @return string
	 */
	public function getDirectory() {
		return $this->directory;
	}

	/**
	 * Getter pro ziskani tridni promenne $parser
	 * @return string cesta k parseru
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * Getter pro ziskani tridni promenne $interpret
	 * @return string cesta k interpretu
	 */
	public function getInterpret() {
		return $this->interpret;
	}


	/**
	 * Getter pro navraceni tridni promenne obsahujici pole testu
	 * @return Test[]
	 */
	public function getTestArray() {
		return $this->testArray;
	}

	/**
	 * Setter pro nastaveni tridni promenne pole testu
	 * @param Test[] $testArray
	 */
	public function setTestArray($testArray) {
		$this->testArray = $testArray;
	}


	/*
	 * Funkce generuje vystupni HTML zdrojovy kod, ktery je vypsan na standardni vystup. Pokud je $stylised true, dojde ke stylizaci textu, jinak dojde k plain html vypisu
	 *
	 * @param bool $stylised Znacka urcujici zda dojde ke stylizaci HTML nebo ne
	 */
	public function generateHtml($stylised) {
		if (!$stylised) {
			$styles = "";
		} else {
			$styles = "			<style>
			
			.failed {color: red;}
			.success {color: darkgreen;}
			.wideTd {width: 250px;}
			.shortTd {width: 180px;}
			
			.left {text-align: left;}
			.center {text-align: center;}
			.right {text-align: right;}
			
			html {
				background-color: #111;
				color: #0074D9;
			}
			
			tr {
				border-width: 1px;
				border-color: black;
			}
			</style>";
		}
		$header = "<!DOCTYPE html>\n<meta charset=\"UTF-8\">\n<head>\n<title>Souhrn testů IPP 2018</title>\n".$styles."\n</head>\n<body>\n<h1>Souhrn testů</h1>\n";
		$header .= "<p>Protokol o výsledku jednotlivých testů pro IPP 2018.</p>\n<h3>Adresář s testy: ".$this->getDirectory()."</h3>\n<h3>Parser: ".$this->getParser()."\n</h3>\n<h3>Interpret: ".$this->getInterpret()."</h3>";
		$footer = "\n</body>\n</html>";

		$innerHtmlHead = "<table>\n<tr><td>Testovací scénář</td><td class='wideTd right'>Výsledek testu</td><td class='shortTd center'>Návratový kód</td><td class='shortTd center'>Očekávaný návratový kód</td></tr>";

		$failed = 0;
		$successful = 0;
		$amountOfTests = count($this->getTestArray());

		foreach ($this->getTestArray() as $test) {
			$innerHtmlHead .= "<tr><td class='left'>".$test->getName()."</td>";

			if ($test->getTestStatus() == "FAIL") {
				$failed++;
				$innerHtmlHead .= "<td class='right failed'>".$test->getTestStatus();
			} else if ($test->getTestStatus() == "SUCCESS") {
				$successful++;
				$innerHtmlHead .= "<td class='right sucess'>".$test->getTestStatus();
			} else {
				$innerHtmlHead .= "<td class='ignored right'>".$test->getTestStatus();
			}
			$innerHtmlHead .= "</td><td class='center result'>".$test->getResultCode()."</td><td class='center result'>".$test->getErc()."</td></tr>\n";

		}
		$innerHtmlBack = "</table>";

		if ($amountOfTests != 0) {
			$amountOfFailedTests = $failed / $amountOfTests;
			$amountOfSuccessfulTests = 1 - $amountOfFailedTests;
		} else {
			$amountOfFailedTests = 0;
			$amountOfSuccessfulTests = 0;
		}
		$statsHtml = "<p>Bylo provedeno ".$amountOfTests." testů.";
		if ($amountOfTests != 0) {
			$statsHtml = $statsHtml."Z nichž bylo <span class='success'>".($amountOfSuccessfulTests*100)."% </span> (<span class='success'>".$successful."</span>) úspěšných a <span class='failed'>".($amountOfFailedTests*100)." %</span> (<span class='failed'>".$failed."</span>) neúspěšných";
		}
		$statsHtml = $statsHtml."</p>";
		$output = $header.$innerHtmlHead.$innerHtmlBack.$statsHtml.$footer;

		//TODO remove next line, we want only output to stdout..
		//file_put_contents("test.html", $output);
		echo $output;
	}
}


$parser = new Parser ($argc, $argv);
$parser->parseArguments();
$parser->printHelp();

$testDir = new TestDirectory($parser->isRF());
$tests = $testDir->createTests($parser->getDirPath());

$testBehavior = new TestBehavior($tests, $parser->getParsePath(), $parser->getInterpretPath());
$tests = $testBehavior->testBehavior();

$html = new HtmlGenerator($tests, $parser->getDirPath(), $parser->getParsePath(), $parser->getInterpretPath(), $parser->getArguments());
$stylised = true; // enable styles
$html->generateHtml($stylised);

exit(0);
