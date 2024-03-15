<?php
namespace Hyperdigital\HdReports\Controller;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Install\Service\CoreVersionService;

class ExternalReportController extends ActionController
{

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    public function __construct(
        ExtensionConfiguration $extensionConfiguration
    ) {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function indexAction()
    {
        $configuration = $this->extensionConfiguration
            ->get('hd_reports');
        $currentIp = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        $return = ['success' => false, 'ip' => $currentIp];
        if (empty($configuration['ipList'])) {
            $listOfAccessIps = [];
        } else {
            $listOfAccessIps = GeneralUtility::trimExplode(',', $configuration['ipList'] ?? '');
        }

        if (GeneralUtility::_GET('access') == trim($configuration['accessPassword'])
            && (empty($listOfAccessIps) || in_array($currentIp, $listOfAccessIps))
        ) {
            $typoVersion = GeneralUtility::makeInstance(Typo3Version::class);
            $coreVersionService = GeneralUtility::makeInstance(CoreVersionService::class);
            $return = ['success' => true];
            $return['version'] = $typoVersion->getVersion();
            $return['latestVersion'] = $coreVersionService->getYoungestPatchRelease()->getVersion();

            if ($coreVersionService->getYoungestPatchRelease()->getVersion() != $typoVersion->getVersion()) {
                $return['versionNeedsUpdate'] = true;
            } else {
                $return['versionNeedsUpdate'] = false;
            }
        }
        return $this->jsonResponse(json_encode($return));
    }
}