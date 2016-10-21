<?php

namespace Kraken\RankingBundle\Command;

use GuzzleHttp\Client as Guzzle;
use Kraken\WarmBundle\Entity\Apartment;
use Kraken\WarmBundle\Entity\House;
use Kraken\WarmBundle\Entity\Wall;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Console\Output\OutputInterface;

class TrackVendorsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ranking:track-vendors')
            ->setDescription('Sprawdza strony firm kotlarskich w poszukiwaniu zmian, martwych odnośników etc.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $q = $em->createQuery('select b from KrakenRankingBundle:Boiler b');
        $iterableResult = $q->iterate();

        $problems = ['site' => [], 'manual' => []];

        foreach ($iterableResult as $row) {
            $boiler = $row[0];
            $name = explode(' ' , $boiler->getName());
            $boilerModel = isset($name[1]) ? $name[1] : $name[0];

            $pageUrl = $boiler->getManufacturerSite();
            $userManualUrl = $boiler->getUserManual();

            $client = new Guzzle();

            try {
                $res = $client->request('GET', $pageUrl);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $output->writeln(sprintf(
                    '<error>%s</error> Zdechł link do strony!',
                    $boiler->getName()
                ));
                $problems['site'][] = $boiler;
            }

            $code = $res->getStatusCode();
            $body = $res->getBody();

            if ($code != 200 || stristr($body, 'nie odnaleziono') || !stristr($body, $boilerModel)) {
                $output->writeln(sprintf(
                    '<error>%s</error> Zdechł link do strony!',
                    $boiler->getName()
                ));
                $problems['site'][] = $boiler;
            } else {
                $output->writeln(sprintf(
                    '<info>%s</info> link do strony żyje',
                    $boiler->getName()
                ));
            }


            if ($userManualUrl) {
                try {
                    $res = $client->request('GET', $userManualUrl);
                } catch (\Exception $e) {
                    $output->writeln(sprintf(
                        '<error>%s</error> Zdechł link do DTR!',
                        $boiler->getName()
                    ));
                    $problems['manual'][] = $boiler;
                }

                $code = $res->getStatusCode();
                $body = $res->getBody();

                if ($code != 200 || !stristr($res->getHeader('Content-Type'), 'pdf')) {
                    $output->writeln(sprintf(
                        '<error>%s</error> Zdechł link do DTR!',
                        $boiler->getName()
                    ));
                    $problems['manual'][] = $boiler;
                } else {
                    $output->writeln(sprintf(
                        '<info>%s</info> link do DTR żyje',
                        $boiler->getName()
                    ));
                }
            }
        }

        $this->notify($problems);
    }

    protected function notify(array $problems)
    {
        if (count($problems['site']) == 0 && count($problems['manual']) == 0) {
            return;
        }

        $body = '<ul>%s</ul>';
        $items = [];

        foreach ($problems['site'] as $boiler) {
            $items[] = sprintf('<li><strong>%s</strong>: strona (<a href="http://ranking.czysteogrzewanie.pl/admin/kraken/ranking/boiler/%d/edit">edytuj kocioł</a>)</li>', $boiler->getName(), $boiler->getId());
        }

        foreach ($problems['manual'] as $boiler) {
            $items[] = sprintf('<li><strong>%s</strong>: DTR (<a href="http://ranking.czysteogrzewanie.pl/admin/kraken/ranking/boiler/%d/edit">edytuj kocioł</a>)</li>', $boiler->getName(), $boiler->getId());
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Ranking: zmiany na stronach')
            ->setFrom([$this->getContainer()->getParameter('mailer_user') => 'CieploWlasciwie.pl'])
            ->setTo('kontakt@czysteogrzewanie.pl')
            ->setContentType('text/html')
            ->setBody(sprintf($body, implode('', $items)))
        ;

        $this->getContainer()->get('mailer')->send($message);
    }
}
