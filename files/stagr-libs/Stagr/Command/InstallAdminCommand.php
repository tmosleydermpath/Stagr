<?php

/*
 * This file is part of the Stagr framework.
 *
 * (c) Gabriel Manricks <gmanricks@me.com>
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stagr\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stagr\Tools\Setup;
use Stagr\Tools\Cmd;

/**
 * Example command for testing purposes.
 */
class InstallAdminCommand extends _Command
{


    protected function configure()
    {
        $this
            ->setName('install-admin')
            ->setDescription('Sets up the Stagr Admin')
            ->addOption('just-update', null, InputOption::VALUE_NONE, 'Set to Skip Installation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Setup::printLogo('Install Admin');

        // check root
        if (posix_geteuid() !== 0) {
            throw new \LogicException("Use 'sudo stagr'!");
        }
        $appName = "000_fortrabbit";
        $setup = new Setup($appName, $output, $this);

        $app = $this->getApplication()->getContainer();

        if ($input->getOption("just-update")) {
            $app->configParam('apps.'. $appName, Setup::$DEFAULT_SETTINGS);
            $setup->setupWebserver();
            $setup->setupMySQL();
        }

        $docRoot = sprintf($setup::APP_WWW_DIR_TMPL . '/htdocs', $appName);

        Cmd::run('rm -R ' . $docRoot);
        Cmd::run('cp -R /vagrant/files/default-site/ ' . $docRoot);
        Cmd::run('chown -R vagrant ' . $docRoot);
    }
}
