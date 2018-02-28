<?php
/**
 * Project: IPP
 * User: Dominik Skala (xskala11)
 * Date: 20.2.18
 * Time: 16:11
 */

class Singleton {

	private $fileName = "test.php";

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


class Common extends Singleton {

	private $argumentCount;
	private $arguments;
	private $hF;
	private $dirPath;
	private $rF;
	private $parsePath;
	private $interpretPath;

	public function __construct($argc, $argv) {
		$this->argumentCount = $argc;
		$this->arguments = $argv;
		$this->hF = false;
		$this->dirPath = "./";
		$this->rF = false;
		$this->parsePath = "./parse.php";
		$this->interpretPath = "./interpret.py";
	}

	/**
	 * @return bool
	 */
	public function isHF() {
		return $this->hF;
	}

	/**
	 * @param bool $hF
	 */
	public function setHF($hF) {
		$this->hF = $hF;
	}

	/**
	 * @return string
	 */
	public function getDirPath() {
		return $this->dirPath;
	}

	/**
	 * @param string $dirPath
	 */
	public function setDirPath($dirPath) {
		$this->dirPath = $dirPath;
	}

	/**
	 *
	 * @return bool
	 */
	public function isRF() {
		return $this->rF;
	}

	/**
	 * @param bool $rF
	 */
	public function setRF($rF) {
		$this->rF = $rF;
	}

	/**
	 * @return string
	 */
	public function getParsePath() {
		return $this->parsePath;
	}

	/**
	 * @param string $parsePath
	 */
	public function setParsePath($parsePath) {
		$this->parsePath = $parsePath;
	}

	/**
	 * @return string
	 */
	public function getInterpretPath() {
		return $this->interpretPath;
	}

	/**
	 * @param string $interpretPath
	 */
	public function setInterpretPath($interpretPath) {
		$this->interpretPath = $interpretPath;
	}

	/**
	 * @return mixed
	 */
	public function getArgumentCount() {
		return $this->argumentCount;
	}

	/**
	 * @return mixed
	 */
	public function getArguments() {
		return $this->arguments;
	}

	public function parseArguments() {
		if ($this->argumentCount != 1) {
			for ($i = 1; $i < $this->argumentCount; $i++) {
				if (preg_match("/--help/", $this->arguments[$i]) == 1 && $this->isHF() != true) {
					$this->setHF(true);
				} else if (preg_match("/--recursive/", $this->arguments[$i]) == 1 && $this->isRF() != true) {
					$this->setRF(true);
				} else if (preg_match("/--directory=.*/", $this->arguments[$i]) == 1 && $this->getDirPath() == "./") {
					$this->setDirPath(substr($this->arguments[$i], 12));
				} else if (preg_match("/--parse-script=.*/", $this->arguments[$i]) == 1 && $this->getParsePath() == "parse.php") {
					$this->setParsePath(substr($this->arguments[$i], 15));
				} else if (preg_match("/--int-script=.*/", $this->arguments[$i]) == 1 && $this->getInterpretPath() == "interpret.py") {
					$this->setInterpretPath(substr($this->arguments[$i], 13));
				} else {
					$this->throwException(10, "Wrong usage of arguments!", true);
				}
			}
			if (($this->getParsePath() != "interpret.py" || $this->getDirPath() != "./" || $this->getParsePath() != "parse.php" || $this->isRF()) && $this->isHF() == true) {
				$this->throwException(10, "Wrong usage of arguments!", true);
			}
		}
	}
}

class Test extends Singleton {
	private $name;
	private $erc;
	private $testStatus;
	private $parser;
	private $interpret;


	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getErc() {
		return $this->erc;
	}

	/**
	 * @param mixed $erc
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
	 * @param mixed $testStatus
	 */
	public function setTestStatus($testStatus) {
		$this->testStatus = $testStatus;
	}

	/**
	 * @return mixed
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * @param mixed $parser
	 */
	public function setParser($parser) {
		$this->parser = $parser;
	}

	/**
	 * @return mixed
	 */
	public function getInterpret() {
		return $this->interpret;
	}

	/**
	 * @param mixed $interpret
	 */
	public function setInterpret($interpret) {
		$this->interpret = $interpret;
	}

	/**
	 * @param array $testResults
	 */
	public function setTestResults($testResults) {
		$this->testResults = $testResults;
	}


	public function inFileExists() {
		if ((file_exists($this->getName().".in") && (is_dir($this->getName().".in"))) || (!file_exists($this->getName().".in"))) {
			return false;
		}
		return true;
	}

	public function outFileExists() {
		if ((file_exists($this->getName().".out") && (is_dir($this->getName().".out"))) || (!file_exists($this->getName().".out"))) {
			return false;
		}
		return true;
	}

	public function rcFileExists() {
		if ((file_exists($this->getName().".rc") && (is_dir($this->getName().".rc"))) || (!file_exists($this->getName().".rc"))) {
			return false;
		}
		return true;
	}

	public function generateInFile() {
		if ((file_put_contents($this->getName().".in", "\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	public function generateOutFile() {
		if ((file_put_contents($this->getName().".out", "\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	public function generateRcFile() {
		if ((file_put_contents($this->getName().".rc", "0\0")) == false) {
			$this->throwException(12, "ERROR writing file", true);
		}
	}

	public function loadErcFromFile() {
		if (($read = file_get_contents($this->getName().".rc")) == false) {
			$this->throwException(12, "ERROR reading file", true);
		}
		return $read;
	}

	public function compareReturnCode($returnCode) {
		return ($this->getErc() == $returnCode ? true : false);
	}
}

class TestBehavior extends Singleton {
	private $testArray;
	private $parser;
	private $interpret;
	private $testResults = array("IGNORE", "SUCCESS", "FAIL");

	/**
	 * TestBehavior constructor.
	 *
	 * @param array $testArray
	 * @param $parser
	 * @param $interpret
	 */
	public function __construct(array $testArray, $parser, $interpret) {
		$this->testArray = $testArray;
		$this->parser = $parser;
		$this->interpret = $interpret;
	}

	/**
	 * @return array
	 */
	private function getTestArray() {
		return $this->testArray;
	}

	/**
	 * @return mixed
	 */
	private function getParser() {
		return $this->parser;
	}

	/**
	 * @return mixed
	 */
	private function getInterpret() {
		return $this->interpret;
	}

	private function compareOutput($expectedOutput, $givenOutput) {
		if (strcmp($expectedOutput, $givenOutput) == 0) {
			return true;
		} else {
			return false;
		}
	}

	private function programExists($file) {
		return (is_file($file) ? true : false);
	}

	/**
	 *
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
					$test->setTestStatus($this->testResults[2]);
					return;
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
					$test->setTestStatus($this->testResults[2]);
					return;
				}
			}

			if ($this->compareOutput(file_get_contents($test->getName()."out"), $output)) {
				// TODO co delat kdyz je output false?

			} else {
			}
		}
	}

}

class TestDirectory extends Singleton {

	private $recursive;
	private $arrayOfTests = array();

	public function __construct($recursive) {
		$this->recursive = $recursive;
	}

	private function isRecursive() {
		return $this->recursive ? true : false;
	}

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


$common = new Common ($argc, $argv);
$common->parseArguments();

$testdir = new TestDirectory($common->isRF());
$tests = $testdir->createTests($common->getDirPath());

$testBehavior = new TestBehavior($tests, $common->getParsePath(), $common->getInterpretPath());
$testBehavior->testBehavior();



exit(0);
