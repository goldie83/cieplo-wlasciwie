<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Vich\GeographicalBundle\VichGeographicalBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Beyerz\OpenGraphProtocolBundle\OpenGraphProtocolBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new Kraken\WarmBundle\KrakenWarmBundle(),
            new Kraken\RankingBundle\KrakenRankingBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
