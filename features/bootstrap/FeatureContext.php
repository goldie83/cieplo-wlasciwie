<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements SnippetAcceptingContext
{
    public function __construct()
    {
    }

    /**
    * @Given I am on domain :domain
    */
    public function iAmOnDomain($domain)
    {
        $this->setMinkParameter('base_url', 'http://'.$domain);
    }

    /**
    * @When I fill in hidden field :field with :value
    */
    public function iFillInHiddenFieldWith($field, $value)
    {
        $this->getSession()->getPage()->find('css', 'input[name="'.$field.'"]')->setValue($value);
    }
}
