<?php
namespace Hyperdigital\HdReports\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Service\CoreVersionService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;

final class AvailableUpdatesPushNotificationCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                'url',
                InputArgument::OPTIONAL,
                'Push notification target URL, available arguments - {availableVersion}, {currentVersion}, {updateAvailable}',
            )
            ->addArgument(
                'content',
                InputArgument::OPTIONAL,
                'Content, available arguments - {availableVersion}, {currentVersion}, {updateAvailable}',
                '{"version":"{currentVersion}","latestVersion":"{availableVersion}","versionNeedsUpdate":{updateAvailable}}'
            )
            ->addArgument(
                'notificationAlways',
                InputArgument::OPTIONAL,
                '(1 or 0) Enable to send notification also when the update is not available',
                0
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $typoVersion = GeneralUtility::makeInstance(Typo3Version::class);
        $coreVersionService = GeneralUtility::makeInstance(CoreVersionService::class);
        $availableUpdate = ($coreVersionService->getYoungestPatchRelease()->getVersion() != $typoVersion->getVersion()) ? true : false;


        if ($input->getArgument('notificationAlways') || $availableUpdate) {
            $url = $input->getArgument('url');
            if (!empty($url)) {
                $url = str_replace('{updateAvailable}', $availableUpdate ? 'true': 'false', $url);
                $url = str_replace('{currentVersion}', $typoVersion->getVersion(), $url);
                $url = str_replace('{availableVersion}', $coreVersionService->getYoungestPatchRelease()->getVersion(), $url);

                $content = $input->getArgument('content');
                $content = str_replace('{updateAvailable}', $availableUpdate ? 'true': 'false', $content);
                $content = str_replace('{currentVersion}', $typoVersion->getVersion(), $content);
                $content = str_replace('{availableVersion}', $coreVersionService->getYoungestPatchRelease()->getVersion(), $content);

                if (!empty($content)) {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => 'Content-Type: application/json',
                            'content' => $content
                        ]
                    ]);

                    $response = file_get_contents($url, false, $context);
                } else {
                    $response = file_get_contents($url);
                }
            }
        }

        return Command::SUCCESS;
    }
}