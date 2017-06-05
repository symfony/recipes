Feature:
  In order to proves that the Behat Symfony extension is correctly installed
  As a user
  I want to have a demo scenario

  Scenario: It receives a response from Symfony's kernel
    When this is a demo scenario that sends a request to "/"
    Then the response should be received
