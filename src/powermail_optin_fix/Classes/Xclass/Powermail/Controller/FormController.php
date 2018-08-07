<?php

namespace Hotbytes\PowermailOptinFix\Xclass\Powermail\Controller;

use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Utility\OptinUtility;

/**
 * Class FormController
 */
class FormController extends \In2code\Powermail\Controller\FormController {

	/**
	 * Forward to formAction if wrong form in plugin variables given
	 *        used in optinConfirmAction()
	 *
	 * @param Mail|null $mail
	 * @return void
	 * @throws StopActionException
	 */
	protected function forwardIfFormParamsDoNotMatchForOptinConfirm(Mail $mail = null) {
		if($GLOBALS['TSFE']->sys_language_content > 0 && is_object($mail) && $mail instanceOf Mail) {
			$mail = $this->_loadAnswers($mail);
		}
		return parent::forwardIfFormParamsDoNotMatchForOptinConfirm($mail);
	}

	/**
	 * Loads the mail answers by given mail. Disables the storage pid check and
	 * sets the language mode to null
	 *
	 * @param Mail $mail
	 * @return Mail
	 */
	protected function _loadAnswers(Mail $mail) {
		/** @var \In2code\Powermail\Domain\Repository\AnswerRepository $answerRepository */
		$answerRepository = $this->objectManager->get('In2code\\Powermail\\Domain\\Repository\\AnswerRepository');

		/** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(false);
		$querySettings->setRespectSysLanguage(false);
		$querySettings->setLanguageMode(null);
		$answerRepository->setDefaultQuerySettings($querySettings);

		$answers = $answerRepository->findByMail($mail->getUid());
		if($answers !== NULL) {
			$objectStorage = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\ObjectStorage');
			foreach($answers as $answer) {
				$objectStorage->attach($answer);
			}
			$mail->setAnswers($objectStorage);
		}

		return $mail;
	}
}
