<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Utility;

use Hfm\Kursanmeldung\Domain\Model\Step2Data;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SessionUtility
{
    private FrontendUserAuthentication $frontendUser;
    private const SES = 'ses';

    public const FORM_SESSION_STEP1_DATA = 'kursanmeldung step1data';
    public const FORM_SESSION_STEP2_DATA = 'kursanmeldung step2data';
    public const FORM_SESSION_STEP3_DATA = 'kursanmeldung step3data';
    public const FORM_SESSION_STEP4_DATA = 'kursanmeldung step4data';
    public const FORM_SESSION_PL = 'kursanmeldung pl';
    public const FORM_SESSION_KURS = 'kursanmeldung kurs';
    public const FORM_SESSION_KURS_UID = 'kursanmeldung kursuid';
    public const FORM_SESSION_SEND_MAIL = 'kursanmeldung sendnnmail';

    /**
     * @param string $sessionKey
     * @param mixed $data
     * @return void
     */
    public function setData(string $sessionKey, mixed $data): void
    {
        // We use type ses to store the data in the session
        $this->getFrontendUser()->setKey(self::SES, $sessionKey, serialize($data));
        // Important: store session data! Or it is not available in the next request!
        $this->getFrontendUser()->storeSessionData();
    }

    /**
     * @param string $sessionKey
     * @return mixed
     */
    public function getData(string $sessionKey): mixed
    {
        $data = $this->getFrontendUser()->getKey(self::SES, $sessionKey);
        if (is_string($data)) {
            return unserialize($data);
        }

        return null;
    }

    /**
     * @return FrontendUserAuthentication
     */
    public function getFrontendUser(): FrontendUserAuthentication
    {
       return $this->frontendUser;
    }

    /**
     * @param FrontendUserAuthentication $frontendUserAuthentication
     * @return void
     */
    public function setFrontendUser(FrontendUserAuthentication $frontendUserAuthentication): void
    {
        // This will create an anonymous frontend user if none is logged in
        $this->frontendUser = $frontendUserAuthentication;
    }

    /**
     * @param \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication $frontendUserAuthentication
     * @return void
     */
    public function cleanSession(FrontendUserAuthentication $frontendUserAuthentication): void
    {
        $this->setFrontendUser($frontendUserAuthentication);
        $this->setData(self::FORM_SESSION_STEP1_DATA, '');
        $this->setData(self::FORM_SESSION_STEP2_DATA, '');
        $this->setData(self::FORM_SESSION_STEP3_DATA, '');
        $this->setData(self::FORM_SESSION_STEP4_DATA, '');
        $this->setData(self::FORM_SESSION_PL, '');
        $this->setData(self::FORM_SESSION_KURS, '');
        $this->setData(self::FORM_SESSION_KURS_UID, '');
        $this->setData(self::FORM_SESSION_SEND_MAIL, '');
    }

    /**
     * @return bool
     */
    public function isCompletedRegistration(): bool
    {
        return empty(!$this->getData(self::FORM_SESSION_STEP3_DATA));
    }
}