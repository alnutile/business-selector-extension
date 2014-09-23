@testingCheckbox @javascript
Feature: Test the token-compatible steps to investigate any issues

  Scenario: Testing checkbox verification steps
    Given I go to "http://127.0.0.1:8081/"
    When I check "option3"
    And I uncheck "option2"
    Then the "option3" checkbox should be checked
    And the "option2" checkbox should not be checked
    And the "option1" checkbox should not be checked
    When I check the "cheeseCheckbox" checkbox
    Then the "cheeseCheckbox" should be checked
    And the "butterCheckbox" should be checked
    And the "milkCheckbox" should not be checked