# SubjectsPlus-shortcode
Repository for the integration of the SubjectsPlus 4.6 with WordPress 6.4. A custom plugin for WordPress using SubjectsPlus API with using shortcodes

Subjectsplus Shortcodes for Wordpress 6.4
========================================

This Wordpress plugin enables shortcodes that can be used to display data 
from the subjectsplus API. 

Setup
----------------------------------------

1. Download the files and place them in your Wordpress plugins folder and activate the plugin
2. After activating the plugin and configuring the SubjectsPlus API KEY and API URL in the WordPress admin dashboard, look for the menu item named "SubjectsPlus4.6". It should appear in the left sidebar menu of the WordPress dashboard.
4. Use the shortcodes in your WordPress pages or posts to display the SubjectsPlus data.


Shortcode usage
-----------------------------------------

The shortcodes use a simple syntax. Each subjectsplus shortcode starts begins with sp:
	[sp]


	[sp service='staff' display='table' max='10']
 
	
This shortcode will query the SubjectsPlus API for staff information and display the results in a table format. You can customize the parameters based on your needs:

    service: Specifies the type of service (e.g., "staff").
    display: Specifies the display format ("table" or "card").
    max: Specifies the maximum number of records to retrieve.

To query the api, you need to choose a service. Currently, subjectsplus has 5 available services. This plugin currently allows you to get staff, guides, and database information. 

	[sp service='staff']
	
	[sp service='guides']  --- upcoming version
	
	[sp service='database'] --- upcoming version
 
	


Staff shortcodes
-------------------------------------------
Filtering by Email:

	[sp service="staff" display="table" max="10" email="example@example.com"]

Filtering by Department:

	[sp service="staff" display="table" max="10" department="Department Name"]

Displaying All Personnel:

	[sp service="staff" display="table" max="10" personnel="all"]
 
Customizing the Number of Staff Members:

	[sp service="staff" display="table" max="5"]

Staff Information (Card Display):

	[sp service="staff" display="card" max="10"]

Staff Information (Table Display):

	[sp service="staff" display="table" max="10"]

You can customize the attributes based on your specific needs. Adjust the service, display, max, email, department, and personnel attributes as required.
