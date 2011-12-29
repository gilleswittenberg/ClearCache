<?php
class ClearCacheShell extends AppShell {
	protected $_deletedFiles = array();

	public function main() {
		$path = TMP . 'cache' . DS;
		$directory = !empty($this->args[0]) ? $this->args[0] : '';
		$this->_clearDir($path . $directory);
		if (!empty($this->_deletedFiles)) {
			foreach ($this->_deletedFiles as $dir => $files) {
				$this->out('Deleted from directory '.$dir.':');
				foreach ($files as $deletedFile) {
					$this->out("\t".$deletedFile);
				}
			}
		} else {
			$this->out('No files deleted');
		}
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Delete cache files from app/tmp/cache');
		$parser->addArgument('directory', array(
			'help' => 'Delete files from given directory only.',
			'choices' => array('models', 'persistent', 'views'),
		));
		return $parser;
	}

	protected function _clearDir($dir) {	
		$ignore = array('.', '..');
		$files = scandir($dir);
		$arr = array();
		foreach ($files as $file) {
			if (in_array($file, $ignore)) {
				continue;
			}
			if (is_dir($dir . DS . $file)) {
				$this->_clearDir($dir . $file);
			} else if (substr($file, 0, 5) == 'cake_') {
				if (unlink($dir . DS . $file)) {
					$arr[] = $dir . DS . $file;
				} else {
					// out deletion failed
				}
			}
		}
		if (!empty($arr)) {
			$this->_deletedFiles[$dir] = $arr;
		}
	}
}
