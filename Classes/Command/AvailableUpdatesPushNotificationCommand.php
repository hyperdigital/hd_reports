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
                'targetEmailAddress',
                InputArgument::OPTIONAL,
                'Email where should lead an info about available update [multiple emails are coma separated]',
            )
            ->addArgument(
                'targetEmailSubject',
                InputArgument::OPTIONAL,
                'Email subject [variables: {availableVersion}]',
            )
            ->addArgument(
                'targetEmailContent',
                InputArgument::OPTIONAL,
                'Email content',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $typoVersion = GeneralUtility::makeInstance(Typo3Version::class);
        $coreVersionService = GeneralUtility::makeInstance(CoreVersionService::class);

        if ($coreVersionService->getYoungestPatchRelease()->getVersion() != $typoVersion->getVersion()) {

            $emailAddress = $input->getArgument('targetEmailAddress');
            $subject = $input->getArgument('targetEmailSubject');
            $content = $input->getArgument('targetEmailContent');

            if (!empty($emailAddress) && !empty($subject)) {
                $subject = str_replace('{availableVersion}', $coreVersionService->getYoungestPatchRelease()->getVersion(), $subject);

                $mail = GeneralUtility::makeInstance(MailMessage::class);
                $mail
                    ->to(
                        new Address($emailAddress)
                    )
                    ->subject($subject)
                    ->text(strip_tags($content))
                    ->html($content)
                    ->send();
            }
        }

        return Command::SUCCESS;
    }
}