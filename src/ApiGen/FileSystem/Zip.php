<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;
use Nette\Utils\Strings;
use RuntimeException;


/**
 * @method setConfig(array $config)
 */
class Zip extends Nette\Object
{

	/**
	 * @var array
	 */
	private $config;


	public function generate()
	{
		if ( ! extension_loaded('zip')) {
			throw new RuntimeException('Extension zip is not loaded');
		}

		$archive = new \ZipArchive;
		if ($archive->open($this->getArchivePath() !== TRUE, \ZipArchive::CREATE)) {
			throw new RuntimeException('Could not open ZIP archive');
		}

		$archive->setArchiveComment(trim(sprintf('%s API documentation generated by %s %s on %s', $this->config['title'], ApiGen::NAME, ApiGen::VERSION, date('Y-m-d H:i:s'))));

		$directory = Strings::webalize(trim(sprintf('%s API documentation', $this->config['title'])), NULL, FALSE);
		$destinationLength = strlen($this->config['destination']);
		foreach ($this->finder->findGeneratedFiles() as $file) {
			if (is_file($file)) {
				$archive->addFile($file, $directory . DS . substr($file, $destinationLength + 1));
			}
		}

		if ($archive->close() === FALSE) {
			throw new RuntimeException('Could not save ZIP archive');
		}

	}


	/**
	 * Returns ZIP archive path.
	 *
	 * @return string
	 */
	public function getArchivePath()
	{
		$name = trim($this->config['title'] . ' API documentation');
		return $this->config['destination'] . DS . Strings::webalize($name) . '.zip';
	}

}
