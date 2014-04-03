Feature: Activity management
  In order to manage my activities
  As a user
  I want to be able to list, view, edit and create activities

  Scenario: Create Activity
    Given I have a activity "Activity 1"
    And I have a task "Task 1"
    And I have a task "Task 2"
    When I add "Activity 1" to "Task 1"
    And I add "Activity 2" to "Task 1"
    Then I should find activity "Activity 1" in task "Task 1"
    And I should find activity "Activity 2" in task "Task 1"
