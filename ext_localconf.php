<?php
defined('TYPO3') or die();

call_user_func(function()
{
    $extensionKey = 'hd_reports';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        "@import 'EXT:hd_reports/Configuration/TypoScript/Reports/setup.typoscript'"
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'HdReports',
        'ExternalReport',
        [
            \Hyperdigital\HdReports\Controller\ExternalReportController::class => 'index',
        ],
        [
            \Hyperdigital\HdReports\Controller\ExternalReportController::class => 'index',
        ]
    );
});