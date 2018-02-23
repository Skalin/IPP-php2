<?php
/**
 * Project: IPP
 * User: skalin
 * Date: 20.2.18
 * Time: 16:11
 */

include("global.php");

$fileName = "test.php";

class Common {

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
					throwException(10, "Wrong usage of arguments!", true);
				}
			}
			if (($this->getParsePath() != "interpret.py" || $this->getDirPath() != "./" || $this->getParsePath() != "parse.php" || $this->isRF()) && $this->isHF() == true) {
				throwException(10, "Wrong usage of arguments!", true);
			}
		}
	}
}

class Test {
	private $name;
	private $returnCode;
	private $something;


	public function __construct($name, $returnCode) {

	}

	private function inFileExists() {
		if ((file_exists($this->name.".in") && (is_dir($this->name.".in"))) || (!file_exists($this->name.".in"))) {
			$this->generateInFile();
		}
	}

	private function outFileExists() {
		if ((file_exists($this->name.".out") && (is_dir($this->name.".out"))) || (!file_exists($this->name.".out"))) {
			$this->generateOutFile();
		}
	}

	private function rcFileExists() {
		if ((file_exists($this->name.".rc") && (is_dir($this->name.".rc"))) || (!file_exists($this->name.".rc"))) {
			$this->generateRcFile();
		}
	}

	private function generateInFile() {
		if (file_put_contents($this->name.".in", "") == false) {
			throwException(12, "ERROR writing file", true);
		}
	}

	private function generateOutFile() {
		if (file_put_contents($this->name.".out", "") == false) {
			throwException(12, "ERROR writing file", true);
		}
	}

	private function generateRcFile() {
		if (file_put_contents($this->name.".rc", "0") == false) {
			throwException(12, "ERROR writing file", true);
		}
	}

}


$common = new Common ($argc, $argv);
$common->parseArguments();

if (scandir($common->getDirPath()) != false) {
	var_dump(scandir($common->getDirPath()));
} else {
	throwException(11, "ERROR opening file".$common->getDirPath(), true);
}
