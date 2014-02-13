<?php
namespace OrangeDigital\BusinessSelectorExtension\Context;

use Behat\MinkExtension\Context\MinkAwareInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Mink\Mink;
use Behat\Behat\Exception\PendingException;
use OrangeDigital\BusinessSelectorExtension\Exception\ElementNotFoundException;

/**
 * This is exposes a number of steps which allow the user to swap business terms
 * specified in a Gherkin file with CSS selectors specified in URL and Selector
 * files.
 *
 * @author James Bodkin
 * @author Ben Waine
 * @author Phill Hicks
 */
class UIBusinessSelectorContext extends BehatContext implements MinkAwareInterface {

    /**
     * Context Parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * Mink Instance
     *
     * @var Mink
     */
    protected $mink;

    /**
     * Mink Parameters
     *
     * @var array
     */
    protected $minkParameters;

    /**
     * Initialises an instance of the UIBusinessSelectorContext
     *
     * @param array $parameters
     *
     * @return void
     */
    public function __construct($parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @Given /^I go to the page "([^"]*)"$/
     */
    public function iGoToThePage($pageName) {
        $page = $this->getUrlFromString($pageName);
        $url = $this->getUrl($page);

        $this->getSession()->visit($url);
    }

    /**
     * @When /^I follow the link "([^"]*)"$/
     * @When /^I press the "([^"]*)" button$/
     * @Given /^I click the "([^"]*)"$/
     */
    public function iFollowTheLink($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);
        $element->click();
    }

    /**
     * @When /^I fill in the "([^"]*)" field with "([^"]*)"$/
     */
    public function iFillInTheFieldWith($elementName, $value) {
        $element = $this->findElementWithBusinessSelector($elementName);
        $element->setValue($value);
    }

    /**
     * @When /^I select "([^"]*)" from the "([^"]*)" selector$/
     */
    public function iSelectFromTheSelector($value, $elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $element->selectOption($value);
    }

    /**
     * @When /^I additionally select "([^"]*)" from the "([^"]*)" selector$/
     */
    public function iAdditionallySelectFromTheSelector($value, $elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);
        $element->selectOption($value, true);
    }

    /**
     * @When /^I check the "([^"]*)" checkbox$/
     */
    public function iCheckTheCheckbox($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        if (!$element->isChecked()) {
            $element->check();
        }
    }

    /**
     * @When /^I uncheck the "([^"]*)" checkbox$/
     */
    public function iUnCheckTheCheckbox($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        if ($element->isChecked()) {
            $element->uncheck();
        }
    }

    /**
     * @Then /^the "([^"]*)" form field should contain "([^"]*)"$/
     */
    public function theFormFieldShouldContain($elementName, $value) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $text = $element->getValue();

        if ($text != $value) {
            throw new \RuntimeException("'$value' does not match expected '$text'");
        }
    }
    
    /**
     * @Then /^I should see "([^"]*)" on the page$/
     */
    public function iShouldSeeOnThePage($arg1) {
        $result = $this->findTextWithBusinessSelector($arg1);
        if (!$result) {
            throw new \RuntimeException("'$arg1' not found on the page");
        }
    }

    /**
     * @Then /^the "([^"]*)" form field should not contain "([^"]*)"$/
     */
    public function theFormFieldShouldNotContain($elementName, $value) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $text = $element->getValue();

        if ($text == $value) {
            throw new \RuntimeException("'$value' does not match expected '$text'");
        }
    }

    /**
     * @Then /^the "([^"]*)" should be checked$/
     */
    public function theShouldBeChecked($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        if (!$element->isChecked()) {
            throw new \RuntimeException("$elementName is not checked");
        }
    }

    /**
     * @Then /^the "([^"]*)" should not be checked$/
     */
    public function theShouldNotBeChecked($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        if ($element->isChecked()) {
            throw new \RuntimeException("$elementName is checked");
        }
    }

    /**
     * @Given /^I attach "([^"]*)" to "([^"]*)"$/
     */
    public function iAttachTo($file, $elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $path = $this->parameters['assetPath'] . $file;
        $rPath = realpath($path);

        if(!file_exists($rPath)) {
            throw new \RuntimeException("File: $rPath does not exist");
        }

        $element->attachFile($rPath);

    }

    /**
     * @When /^I hover over "([^"]*)"$/
     */
    public function iHoverOver($elementName) {
        $element = $this->findElementWithBusinessSelector($elementName);
        $element->mouseOver();
    }

    /**
     * @When /^I focus on the "([^"]*)" iframe$/
     */
    public function iFocusOnTheIframe($elementName) {
        $element = $this->getSelectorFromString($elementName);

        $session = $this->getSession();
        $session->switchToIFrame($element);
    }

    /**
     * @When /^I refocus on the primary page$/
     */
    public function iRefocusOnThePrimaryPage() {
        $session = $this->getSession();
        $session->switchToIFrame();
    }

    /**
     * @Then /^I should see "([^"]*)" component$/
     */
    public function iShouldSeeComponent($elementName) {
        $this->findElementWithBusinessSelector($elementName);
    }

    /**
     * @Then /^I should not see "([^"]*)" component$/
     */
    public function iShouldNotSeeComponent($elementName) {
        try {
            $result = $this->findElementWithBusinessSelector($elementName);

            if (!is_null($result)) {

                if($result->isVisible()) {
                    throw new \RuntimeException("Component $elementName found");
                }
            }
        } catch (ElementNotFoundException $e) {
            return;
        }
    }

    /**
     * @When /^I wait for the "([^"]*)" component to (dis|)appear$/
     */
    public function waitForComponent($elementName, $expVisibility = null) {
        // Visibility is null when the 'dis' section of the string is not present
        $expVisibility = (empty($expVisibility)) ? 'visible' : 'hidden';

        $selector = $this->getSelectorFromString($elementName);

        $session = $this->getSession();
        $timeout = $this->getTimeout();

        if ($expVisibility == 'hidden') {
            // If we are expecting the element to disappear it could either have its visibility changed or removed from the DOM
            $condition = "window && window.jQuery && (jQuery('$selector').is(':" . $expVisibility . "') || jQuery.find('$selector').length == 0);";
        } else {
            $condition = "window && window.jQuery && jQuery('$selector').is(':" . $expVisibility . "');";
        }

        // Will block for $timeout or until the the condition return true
        // Always returns true never throws an exception.
        $session->wait($timeout, $condition);

        // Search for element. Element if found, null if element not found.
        $element = $session->getPage()->find('css', $selector);

        if (!is_null($element)) {
            // Element can be on the page but may not be visible
            $visibility = $element->isVisible();

            if ($expVisibility == 'hidden' && $visibility) {
                throw new \RuntimeException("Component " . $elementName . " is visible");
            } elseif ($expVisibility == 'visible' && !$visibility) {
                throw new \RuntimeException("Component " . $elementName . " is on page but not visible");
            }

        } else {
            // No Element on the page
            if($expVisibility == 'visible') {
                throw new \RuntimeException("Component " . $elementName . " does not appear on the page");
            }
        }

    }

    /**
     * @Then /^the "([^"]*)" should contain "([^"]*)"$/
     */
    public function theShouldContain($elementName, $text) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $actualText = $element->getText();

        if (strpos($actualText, $text) === FALSE) {
            throw new \RuntimeException("'$text' not found in $elementName");
        }
    }

    /**
     * @Then /^the "([^"]*)" should not contain "([^"]*)"$/
     */
    public function theShouldNotContain($elementName, $text) {
        $element = $this->findElementWithBusinessSelector($elementName);

        $actualText = $element->getText();

        if (strpos($actualText, $text) !== FALSE) {
            throw new \RuntimeException("'$text' not found in $elementName");
        }
    }

    /**
     * @Then /^"([^"]*)" should contain "([^"]*)"$/
     */
    public function shouldContain($elementNameOutter, $elementNameInner) {
        $scopeElement = $this->findElementWithBusinessSelector($elementNameOutter);

        $element = $this->findElementWithBusinessSelector($elementNameInner, $scopeElement);
    }

    /**
     * @Then /^"([^"]*)" should not contain "([^"]*)"$/
     */
    public function shouldNotContain($elementNameOutter, $elementNameInner) {
        $scopeElement = $this->findElementWithBusinessSelector($elementNameOutter);

        try {
            $result = $this->findElementWithBusinessSelector($elementNameInner, $scopeElement);

            if (!is_null($result)) {
                throw new \RuntimeException("Element $elementNameInner found in $elementNameOutter");
            }
        } catch (ElementNotFoundException $e) {
            return;
        }
    }

    /**
     * Checks in the selector yaml file for a selector which matches the 
     * supplied business friendly string. 
     *
     * @return string
     */
    public function getSelectorFromString($string) {
        $selectors = $this->getSelectorHash();

        if (!array_key_exists($string, $selectors)) {
            throw new \RuntimeException('Selector: ' . $string . ' not found in selectors file');
        }

        return $selectors[$string];
    }

    /**
     * Checks in the URL yaml file for a URL which matches the supplied
     * bussiness friendly string.
     *
     * @return string 
     */
    public function getUrlFromString($string) {
        $urls = $this->getURLHash();

        if (!array_key_exists($string, $urls)) {
            throw new \RuntimeException('URL: ' . $string . ' not found in urls file');
        }

        return $urls[$string];
    }

    /**
     * Returns an array of businesTerm > Selectors.
     * Checks if they have been loaded and loads them if not.
     *
     * @return array
     */
    protected function getSelectorHash() {
        if (!isset($this->selectors)) {
            // Load Selectors from file
            $path = $this->parameters['selectorFilePath'];

            if (!$path) {
                throw new \RuntimeException('Value "selectorFilePath not set in config"');
            }

            $this->selectors = $this->loadYaml($path);
        }

        return $this->selectors;
    }

    /**
     * Returns an array of businesTerm > URLs.
     * Checks if they have been loaded and loads them if not.
     *
     * @return array
     */
    protected function getURLHash() {
        if (!isset($this->urls)) {
            // Load Selectors from file
            $path = $this->parameters['urlFilePath'];

            if (!$path) {
                throw new \RuntimeException('Value "urlFilePath not set in config"');
            }

            $this->urls = $this->loadYaml($path);
        }

        return $this->urls;
    }

    /**
     * Loads yaml and returns the result
     *
     * @param string $path 
     *
     * @return array
     */
    protected function loadYaml($path) {
        if (!file_exists($path)) {
            throw new \RuntimeException('File: ' . $path . ' does not exist');
        }

        $parser = new \Symfony\Component\Yaml\Parser();

        $string = file_get_contents($path);

        $result = $parser->parse($string);

        if (!$result) {
            throw new \RuntimeException('Unable to parse ' . $path);
        }

        return $result;
    }

    /**
     * Gets the current Mink session.
     *
     * @return \Behat\Mink\Session
     */
    protected function getSession() {
        return $this->mink->getSession();
    }

    /**
     * Returns a fully qualified URL using the base URL passed to Mink.
     *
     * @param string $frag
     *
     * @return string 
     */
    protected function getUrl($frag = null) {
        $url = $this->minkParameters['base_url'];

        if (!is_null($frag) && $frag != '/') {
            $url = $url . $frag;
        }

        return $url;
    }

    /**
     * Returns a timeout value used for element conditionals either from the
     * config or if not set, provides a default.
     *
     * @return string
     */
    protected function getTimeout() {
        if (isset($this->parameters['timeout'])) {
            return $this->parameters['timeout'].'000';
        } else {
            return "30000";
        }
    }

    /**
     * Finds an element from the current page using the supplied business
     * selector. 
     * 
     * @param string $elementName
     * @param NodeElement $scopeElement If passed search is conducted within the supplied element.
     * 
     * @return NodeElement|null 
     */
    protected function findElementWithBusinessSelector($elementName, $scopeElement = null) {
        $selector = $this->getSelectorFromString($elementName);

        if (is_null($scopeElement)) {
            $session = $this->getSession();
            $scopeElement = $session->getPage();
        }

        $result = $scopeElement->find('css', $selector);

        if (is_null($result)) {
            throw new ElementNotFoundException("Element $elementName using selector $selector not found");
        }

        return $result;
    }

    /**
     * Looks for text on the page. Great for Translation based text search.
     *
     * @param string $text
     *
     * @return NodeElement|null
     */
    protected function findTextWithBusinessSelector($textToFind) {
        $text = $this->getSelectorFromString($textToFind);

        $session = $this->getSession()->getPage();
        $result = $session->hasContent($text);

        if (is_null($result)) {
            throw new ElementNotFoundException("Text $textToFind using text $text not found");
        }

        return $result;
    }
    
    /**
     * Sets Mink instance.
     *
     * @param Mink $mink Mink session manager
     */
    public function setMink(Mink $mink) {
        $this->mink = $mink;
    }

    /**
     * Sets parameters provided for Mink.
     *
     * @param array $parameters
     */
    public function setMinkParameters(array $parameters) {
        $this->minkParameters = $parameters;
    }

}
