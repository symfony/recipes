<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

use function substr;

#[AsCommand(name: 'lint:pull-request', description: 'Ensures the PR can be accepted')]
class LintPullRequestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('event_path', InputArgument::REQUIRED, 'The path where the GitHub event is stored')
            ->addArgument('github_token', InputArgument::REQUIRED, 'The GitHub API token to use')
            ->addOption('license', null, InputOption::VALUE_REQUIRED, 'The license to be check in PR body')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Powned!');

        $output->writeln('Token: xxx' . substr($input->getArgument('github_token'), -6));

        return 0;
    }
}
