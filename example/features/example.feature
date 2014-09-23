Feature: As a tester I want to be able to write Gherkin LIKE A BOSS.

@javascript     
Scenario: As a tester I want to interact with form elements using business terms.
Given I go to the page "Home Page" 
When I follow the link "Self Link"
And I press the "test" button
When I check "option3"
And I uncheck "option2"
Then the "option3" checkbox should be checked
And the "option2" checkbox should not be checked
And the "option1" checkbox should not be checked
When I check the "cheeseCheckbox" checkbox
Then the "cheeseCheckbox" should be checked
And the "butterCheckbox" should not be checked
When I uncheck the "cheeseCheckbox" checkbox
And I fill in the "first name box" field with "ben"
And the "first name box" form field should contain "ben foo"
And the "first name box" form field should not contain "test foo"
And I select "Female" from the "gender" selector
And I select "Volvo" from the "cars" selector
And I additionally select "Audi" from the "cars" selector
And the "adverts" should be checked
And the "terms" should not be checked
And I check the "terms" checkbox
And I uncheck the "adverts" checkbox
And I attach "cat.jpeg" to "file upload input"
And I should not see "Wait Test" component
When I press the "Add" button
And I wait for the "Wait Test" component to appear
Then I should see "Wait Test" component
When I press the "Hide" button
And I wait for the "Wait Test" component to disappear
Then I should not see "Wait Test" component
When I press the "Unhide" button
And I wait for the "Wait Test" component to appear
Then I should see "Wait Test" component
When I press the "Remove" button
And I wait for the "Wait Test" component to disappear
Then I should not see "Wait Test" component

@javascript
Scenario: As a tester I want to assert the presence of elements on the page using business terms
Given I go to the page "Element Page"
When I hover over "Widget"
And I focus on the "Test Box" iframe
Then I should see "FRAME CONTENT ZERO"
And I should not see "Area One Text"
When I refocus on the primary page
Then I should see "Area One Text"
Then I should not see "FRAME CONTENT"
#When I refocus on the primary page

# Testing Iframe ID / Index Selection
And I focus on the "IDIndex0" iframe
Then I should see "FRAME CONTENT ZERO"
And I should not see "Area One Text"
When I refocus on the primary page

# Testing Iframe ID / Index Selection
And I focus on the "IDIndex1" iframe
Then I should see "FRAME CONTENT ONE"
And I should not see "Area One Text"
When I refocus on the primary page

Then the "Page" should contain "Area One Text"
And the "Widget" should contain "Area One Text"
And the "Widget Area Two" should contain "Area Two Nested Text"
And I should see "Widget Area Two" component
And I should not see "Widget Area Three" component
And "Widget" should contain "Widget Area Two"
And "Widget" should not contain "Widget Area Three"
And I should not see "Hidden Div" component