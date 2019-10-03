# legoapp
A Lego app for finding sets based on certain bricks.

# Usage
This one page app uses jQuery to make AJAX calls via an api route with the Rebrickable database. The main aim is to find sets you may have based on a selection of parts from a bulk job lot. The best practice is to find either unique parts and colours and work your way up from there. Due to limits in place and how the external APIs work, there is a limit of 8 parts per search.

# Relevant files
	\app\Http\Controllers\legoapp.php - the main controller for web views and AJAX calls that make use of sessions and blade views
	\app\Http\Controllers\legoapi.php - the main controller for AJAX calls with json returns
	\app\RebrickableAPI - the class that makes calls to the Rebrickable API
	\resources\js\components - Vue components and javascript functionality (mostly jQuery and it should not be mixed with Vue components)

# About the code
Before starting this task I only had a basic knowledge of Javascript frameworks (being mainly a back-end developer). I was going to write this application using vue.js while using jQuery for AJAX calls. I realised this was a big mistake in terms of how the framework was meant to be used and as such it is mostly a jQuery application.

As the API used isn't well known I wrote the classes to interact with it from scratch. Some unique features include saving JSON responses to limit the number of calls to their API as the response times can be reasonably long. As Lego items are rarely updated, data is only refreshed on a daily basis and records from previos days are deleted via a Laravel scheduled task. No user data is attached to each search and it is fully sanitised to prevent any damage from potential attacks.

Another stumbling block is the fact that getting the desired result has required some creative use of the API. Unfortunately it does not let you search for sets that include a set of different parts. I need to fetch all sets each selected part is used in and then use PHP to calculate which sets all the parts are used in.


