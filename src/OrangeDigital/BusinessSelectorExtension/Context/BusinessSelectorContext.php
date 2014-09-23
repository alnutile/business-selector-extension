<?php

namespace OrangeDigital\BusinessSelectorExtension\Context;

use Behat\Behat\Context\BehatContext;

/**
 * Controlling context which loads other contexts specified in the behat.yml
 * file.
 *
 * @author James Bodkin
 * @author Ben Waine
 * @author Phill Hicks
 */
class BusinessSelectorContext extends BehatContext {

    /**
     * Parameters from behat.yml.
     *
     * @var array
     */
    private $parameters;

    /**
     * Gets a parameter.
     *
     * @param string $extension
     * @param string $name
     *
     * @return string
     */
    public function getParameter($extension, $name) {
        return $this->parameters[$extension][$name];
    }

    /**
     * Checks a parameter.
     *
     * @param string $extension
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($extension, $name) {
        return isset($this->parameters[$extension][$name]);
    }

    /**
     * Sets a parameter.
     *
     * @param string $extension
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public function setParameter($extension, $name, $value) {
        $this->parameters[$extension][$name] = $value;
    }

    /**
     * Initialises the subcontexts and passes them the parameters.
     *
     * @param array $parameters 
     *
     * @return void
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters['contexts'];

        foreach ($this->parameters as $name => $values) {
            $className = __NAMESPACE__ . '\\' . ucfirst($name) . 'Context';
            $this->useContext($name, new $className($parameters));
        }
    }

}
