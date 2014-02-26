# Business Selectors For Behat

[![Build Status](https://travis-ci.org/alnutile/business-selector-extension.png?branch=develop)](https://travis-ci.org/alnutile/business-selector-extension)

## Introduction 

--

The original project can be seen here
https://github.com/orangedigital/business-selector-extension

And many notes still apply below. I needed to simplify it a bit for the project I am using it on though it still is extendable to to other projects.

Basically I do not want errors thrown if a token is not found since it can default back to the string the user used and then let Behat error out as needed

--

The Mink Extension for Behat is an excellent way to quickly get up and running 
with web acceptance testing. It allows the user to specify HTML elements on the
page using common attributes like 'title', 'alt' and 'value'. Some steps allow
the use of CSS selectors inline in the Gherkin files. 

While this approach is great it has a few draw backs: 

1) The attribute values may not always be the most business friendly and can 
reduce the readability of the Gherkin scenarios.

2) Sometimes it isn't possible to identify an element using the attributes 
the MinkExtension provides. 

3) By using these attributes directly in the Gherkin files, they become tightly 
coupled to the front end implementation. Gherkin should express business 
requirements rather than implementation details.      

Business Selectors for Behat allow the user to use steps very similar to the 
ones provided by the MinkExtension to describe elements on the page in business
terms. At the time the test is run the business terms are swapped for a CSS 
selector which is maintained in a yml file.  

### Example

#### Gherkin Written With Steps Provided By BusinessSelectorsExtension

````cucumber
Given I go to the page "Home Page" 
When I follow the link "Add Subscription"
And I fill in the "first name box" field with "ben"
Then the "first name box" form field should contain "ben"
And I press the "test" button
And the "Widget" should contain "Area One Text"
````
The quoted values map to the values in the file configured in behat.yml 
by the parameter 'selectorFilePath'. 

````yaml
Add Subscription: "a.self-link"
first name box: "form#name_form input[name=first_name]"
test: "form#name_form input[name=submit]"
Widget: div#user_widget
````

## Installation

The best way to install the contexts is by using composer. 

1) For instructions on installing Behat see: [behat.org](http://behat.org/)

2) Add in the following to the composer.json of the project you'd like to test.

````javascript
{   
    "minimum-stability": "dev",
    "require": {
        "behat/behat": ">v2.4@stable",
        "orangedigital/business-selector-extension": "*"
    }
}
````

3) Add the OrangeDigital\BusinessSelectorExtension\Extension section to your
behat.yml file as in the example below.

````yaml

default:
    context:
        parameters:
            javascript_session: selenium
            browser: firefox
            show_cmd: open %s
    extensions:
        OrangeDigital\BusinessSelectorExtension\Extension:
          urlFilePath: urls.yml
          selectorFilePath: selectors.yml
          assetPath: path
          timeout: 30
          contexts:
            UIBusinessSelector: ~
        Behat\MinkExtension\Extension:
            base_url: "http://local.dev"
            default_session: goutte
            goutte: ~
            selenium: ~
            selenium2: ~

````

4) Amend your FeatureContext file to use the BusinessSelectorContext file. 

````php 
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
}


````

## Installation: Examples ##

Install the provided examples by moving the html files in *'example/www'* to the 
web root of a local webserver or create a vhost pointing to *'example/www'*. 

Edit *'example/behat.yml'* *'base_url'* to point to your the url of web root above.

Run **../bin/behat**

##Steps Provided By Business Selectors

Bellow are a list of steps provided by the business selector extension. Values in 
\<BRACKETS\> denote arbitrary business friendly names which should match CSS 
selectors in the relevant config file (specified in behat.yml by 'urlFilePath' 
and 'selectorFilePath'). See the provided example implementation for details.   

**The URL below should be placed in the URLS file specified in behat.yml by the 
parameter "urlFilePath"**

````cucumber
Given I go to the page "<PAGE NAME>" 
````

````cucumber
Given I attach "<FILE NAME> to <IMAGE INPUT>"
````
**Note: This requires the assetPath to be configured to a relative directory that contains your assets (e.g. assetPath: assets/). Filename must include the extension associated with the file (e.g. example.jpeg).**

**All CSS Selectors below should be placed in the CSS selector file specified
in behat.yml by the parameter "selectorFilePath"**


````cucumber
When I follow the link "<LINK>"
````

````cucumber
When I click the "<CLICKABLE ELEMENT>"
````

````cucumber
When I press the "<BUTTON>" button
````

````cucumber
When I fill in the "<TEXT INPUT>" field with "value"
````

````cucumber
When the "<TEXT INPUT>" form field should not contain "value"
````

````cucumber
When I select "value" from the "<SELECT OR MULTISELECT>" selector
````

````cucumber
When I additionally select "value" from the "<MULTISELECT>" selector
````

````cucumber
When I check the "<CHECKBOX>" checkbox
````

````cucumber
When I uncheck the "<CHECKBOX>" checkbox
````

````cucumber
When I focus on the "<IFRAME (RELATES TO ID OR INDEX)>" iframe
````
**Note: The step above is the only step where the selector MUST be an ID. 
If the iframe does not have an ID, you can provide the index of the iframe, where
0 relates to the first iframe, 1 the second etc. An example of this can be found
in example/features/example.feature**

````cucumber
When I refocus on the primary page
`````

````cucumber
When I hover over "<PAGE ELEMENT>"
````

````cucumber
When I wait for the "<PAGE ELEMENT>" component to [dis]appear
````

````cucumber
Then the "<PAGE ELEMENT>" should contain "value"
````

````cucumber
Then the "<PAGE ELEMENT>" should not contain "value"
````

````cucumber
Then I should see "<PAGE ELEMENT>" component
````

````cucumber
Then I should not see "<PAGE ELEMENT>" component
````

````cucumber
Then "<PAGE ELEMENT>" should contain "<PAGE ELEMENT>"
````

````cucumber
Then "<PAGE ELEMENT>" should not contain "<PAGE ELEMENT>"
````

````cucumber
Then the "<CHECKBOX>" should be checked
````

````cucumber
Then the "<CHECKBOX>" should not be checked
````

````cucumber
Then the "<TEXT INPUT>" form field should contain "value"
````
