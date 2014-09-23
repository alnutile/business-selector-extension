<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext,
    OrangeDigital\BusinessSelectorExtension\Context\BusinessSelectorContext;

class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('mink', new MinkContext($parameters));
        $this->useContext('BusinessSelectors', new BusinessSelectorContext($parameters));
    }

    /**
     * @Given /^I wait for (\d+) seconds$/
     */
    public function iWaitForSeconds($arg1)
    {
        sleep($arg1);
    }
}
