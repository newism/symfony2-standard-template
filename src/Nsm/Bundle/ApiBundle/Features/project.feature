Feature: Testing the RESTfulness of the Project Controller
  In order to update Projects programatically
  As an api user
  I need to access the projects api

  Scenario: Browsing Projects
    Given I go to "/projects"
    And request content type "application/json"
    Then the response status code should be 200
    And the response header should contain "application/json"
