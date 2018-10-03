<?php
/**
 * PH2M_Gdpr
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gdpr
 * @copyright  Copyright (c) 2018 PH2M SARL
 * @author     PH2M SARL <contact@ph2m.com> : http://www.ph2m.com/
 *
 */


class PH2M_Gdpr_Block_Cookies_Config extends Mage_Core_Block_Template
{
    /**
     * Initialize cache
     *
     * @return null
     */
    protected function _construct()
    {
        parent::_construct();
        /*
        * setting cache to save the core config
        */
        $this->setCacheTags([Mage_Core_Model_Config::CACHE_TAG]);
        $this->setCacheLifetime('86400');
    }

    /**
     * Return url of privacy page
     *
     * @return string
     */
    public function getPrivacyPageUrl()
    {
        if (!$cmsPageId = Mage::getStoreConfig('phgdpr/cookies/policy_cms_page')) {
            return '';
        }

        return Mage::helper('cms/page')->getPageUrl($cmsPageId);
    }

    /**
     * @return bool|string
     */
    public function getCustomWording()
    {
        $customWording = [];
        if ($alertBigPrivacy = Mage::getStoreConfig('phgdpr/cookies/wording_alert_big_privacy')) {
            $customWording['alertBigPrivacy'] = $alertBigPrivacy;
        }
        if ($alertAcceptAll = Mage::getStoreConfig('phgdpr/cookies/wording_accept_all')) {
            $customWording['acceptAll'] = $alertAcceptAll;
        }
        if ($alertPersonalize = Mage::getStoreConfig('phgdpr/cookies/wording_personalize')) {
            $customWording['personalize'] = $alertPersonalize;
        }

        if (count($customWording) < 1) {
            return false;
        }

        return Mage::helper('core')->jsonEncode($customWording);
    }

    /**
     * @return bool
     */
    public function enableGAService()
    {
        if (!Mage::getStoreConfigFlag('phgdpr/cookies/enable_google_analytics') || !Mage::getStoreConfigFlag('google/analytics/active')) {
            return false;
        }
        return true;
    }
}
