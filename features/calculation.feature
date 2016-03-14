Feature: Energy balance calculation
  In order to get energy balance calculation
  as a human being
  I want to be able to come here, fill out the form and get that data in my face

  Scenario: Energy calculation for single house
    Given I am on domain "cieplo.dev"
    Given I go to "/"
     When I follow "Sprawdź swój dom!"
      And I select "single_house" from "calculation[building_type]"
      And I press "Dalej"
     Then I should see "Położenie budynku w przestrzeni i czasie"
     When I fill in hidden field "calculation[latitude]" with "51.09"
      And I fill in hidden field "calculation[longitude]" with "16.98"
      And I select "1980" from "calculation[construction_year]"
      And I press "Dalej"
     Then I should see "Powierzchnia zabudowy"
     When I select "yes" from "calculation[has_area]"
      And I fill in "calculation[area]" with "100"
      And I select "Parterowy" from "calculation[building_floors]"
      And I select "Skośny" from "calculation[building_roof]"
      And I select "1" from "calculation_building_heated_floors_1"
      And I select "2" from "calculation_building_heated_floors_2"
      And I press "Dalej"
     Then I should see "Ściany zewnętrzne"
     When I select "traditional" from "calculation[construction_type]"
      And I fill in "calculation[wall_size]" with "50"
      And I select "Cegła pełna" from "calculation[primary_wall_material]"
      And I check "calculation[has_isolation_outside]"
      And I select "Styropian" from "calculation[external_isolation_layer][material]"
      And I fill in "calculation[external_isolation_layer][size]" with "15"
      And I select "Współczesne dwuszybowe" from "calculation[windows_type]"
      And I select "Nowe drewniane" from "calculation[doors_type]"
      And I fill in "calculation[number_windows]" with "10"
      And I fill in "calculation[number_doors]" with "2"
      And I press "Dalej"
     Then I should see "Poddasze i parter"
     When I select "Wełna mineralna" from "calculation[top_isolation_layer][material]"
      And I fill in "calculation[top_isolation_layer][size]" with "30"
      And I select "Styropian twardy (XPS)" from "calculation[bottom_isolation_layer][material]"
      And I fill in "calculation[bottom_isolation_layer][size]" with "10"
      And I press "Dalej"
     Then I should see "Jaką temperaturę podać?"
     When I fill in "calculation[indoor_temperature]" with "21"
      And I press "Wynik"
     Then I should see "A.D. lata 80-te, 170m2 ogrzewane, Otwock i okolice"
      And I should see "50cm, konstrukcja: cegła pełna, izolacja: styropian 15cm"
      And I should see "dach skośny, Wełna mineralna 30cm"
      And I should see "7kW to potrzebna moc grzewcza (C.O. bez CWU)"

  Scenario: Energy calculation for apartment
    Given I am on domain "cieplo.dev"
    Given I go to "/"
     When I follow "Sprawdź swój dom!"
      And I select "apartment" from "calculation[building_type]"
      And I press "Dalej"
     Then I should see "Położenie budynku w przestrzeni i czasie"
     When I fill in hidden field "calculation[latitude]" with "53.7746888"
      And I fill in hidden field "calculation[longitude]" with "20.5262375"
      And I select "1939" from "calculation[construction_year]"
      And I press "Dalej"
     Then I should see "Powierzchnia mieszkania"
      And I fill in "calculation[area]" with "40"
      And I select "Jednopoziomowe" from "calculation[building_floors]"
      And I check "I poziom"
      And I uncheck "II poziom"
      And I uncheck "III poziom"
      And I press "Dalej"
     Then I should see "Ściany zewnętrzne"
     When I select "traditional" from "calculation[construction_type]"
      And I fill in "calculation[wall_size]" with "40"
      And I select "Cegła pełna" from "calculation[primary_wall_material]"
      And I select "Współczesne dwuszybowe" from "calculation[windows_type]"
      And I select "Nowe drewniane" from "calculation[doors_type]"
      And I fill in "calculation[number_windows]" with "10"
      And I fill in "calculation[number_doors]" with "2"
      And I press "Dalej"
     Then I should see "Sąsiedztwo"
      And I select "Świat zewnętrzny" from "calculation[whats_over]"
      And I select "Ogrzewany lokal" from "calculation[whats_under]"
     When I select "Wełna mineralna" from "calculation[top_isolation_layer][material]"
      And I fill in "calculation[top_isolation_layer][size]" with "30"
      And I select "Styropian twardy (XPS)" from "calculation[bottom_isolation_layer][material]"
      And I fill in "calculation[bottom_isolation_layer][size]" with "10"
      And I select "Z dwóch" from "calculation[number_external_walls]"
      And I select "Z jednej" from "calculation[number_unheated_walls]"
      And I press "Dalej"
     Then I should see "Jaką temperaturę podać?"
     When I fill in "calculation[indoor_temperature]" with "21"
      And I press "Wynik"
      And show last response
     Then I should see "A.D. gdzieś przed II wojną, 40m2 ogrzewane, Otwock i okolice"
      And I should see "40cm, konstrukcja: cegła pełna, izolacja: brak"
      And I should see "Ściany zewnętrzne: 2"
      And I should see "6kW to potrzebna moc grzewcza (C.O. bez CWU)"
