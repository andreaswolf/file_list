<?php
namespace Causal\FileList\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Xavier Perseguers <xavier@causal.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * File controller.
 *
 * @category    Controller
 * @package     TYPO3
 * @subpackage  tx_filelist
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class FileController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * Listing of files.
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\Folder $folder The folder to display.
	 * @return void
	 */
	public function listAction($folder = NULL) {
		$inSubfolder = $folder !== NULL;
		$pathToList = $this->settings['path'];
		$rootFolder = $this->getFolderToDisplay($pathToList);

		if ($folder !== NULL) {
			// We have an Extbase folder abstraction, but we need the original folder, as Extbase does not expose all
			// the methods
			$folder = $folder->getOriginalResource();

			if (!$folder->getStorage()->isWithinFolder($rootFolder, $folder)) {
				// illegal access!
				throw new \RuntimeException("Requested folder is not within root folder", 1439756698);
			}
			if ($folder->getCombinedIdentifier() === $rootFolder->getCombinedIdentifier()) {
				$inSubfolder = FALSE;
			}
		} else {
			$folder = $rootFolder;
		}

		$this->view->assign('folder', $folder);
		$this->view->assign('inFolder', $inSubfolder);
	}

	/**
	 * @param $pathToList
	 * @return \TYPO3\CMS\Core\Resource\FileInterface|\TYPO3\CMS\Core\Resource\Folder
	 */
	protected function getFolderToDisplay($pathToList) {
		if (substr($pathToList, 0, 5) == 'file:') {
			$pathToList = substr($pathToList, 5);
		}
		/** @var ResourceFactory $storageFactory */
		$storageFactory = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\ResourceFactory');
		$storage = $storageFactory->getStorageObjectFromCombinedIdentifier($pathToList);

		$folder = $storageFactory->retrieveFileOrFolderObject($pathToList);

		return $folder;
	}

}
