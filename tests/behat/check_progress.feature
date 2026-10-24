@mod @mod_vocabcoach @javascript
Feature: Progress dots on the check page
  In order to see how far I am through a check
  As a student
  I need each dot to take the colour of my answer

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity   | name         | course | idnumber |
      | vocabcoach | Vocabcoach 1 | C1     | VC1      |
    And the following "mod_vocabcoach > vocabs" exist:
      | activity | user     | front | back  |
      | VC1      | student1 | dog   | Hund  |
      | VC1      | student1 | cat   | Katze |
      | VC1      | student1 | bird  | Vogel |

  Scenario: Answering a word colours the next dot
    Given I am on the "VC1" "mod_vocabcoach > Check" page logged in as "student1"
    Then the "class" attribute of "//div[@id='progress-bar']/div[1]/div" "xpath_element" should not contain "dot-"
    And the "class" attribute of "//div[@id='progress-bar']/div[2]/div" "xpath_element" should not contain "dot-"
    And the "class" attribute of "//div[@id='progress-bar']/div[3]/div" "xpath_element" should not contain "dot-"
    When I press "Got it!"
    Then the "class" attribute of "//div[@id='progress-bar']/div[1]/div" "xpath_element" should contain "dot-green"
    And the "class" attribute of "//div[@id='progress-bar']/div[2]/div" "xpath_element" should not contain "dot-"
    When I press "Didn't know that!"
    Then the "class" attribute of "//div[@id='progress-bar']/div[2]/div" "xpath_element" should contain "dot-red"
    And the "class" attribute of "//div[@id='progress-bar']/div[1]/div" "xpath_element" should contain "dot-green"
    And the "class" attribute of "//div[@id='progress-bar']/div[3]/div" "xpath_element" should not contain "dot-"
