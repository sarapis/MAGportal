# MAG management portal

https://mutualaid.nyc/manage-your-group/


## Installation

By default it is supposed that your server root folder is /var/www/html

    cd /var/www
	git clone https://github.com/sarapis/WeGov_MAGadmin.git
    cd composed
    composer update
	
	
Create your instance of Airtable data template `https://airtable.com/universe/expqKAN7LQxhMqqsc/mutual-aid-groups-portal`

Open instance, go to Help > API documentation

Copy Airtable doc ID from API documentation url. For https://airtable.com/appAaBbCcDdEeFf/api/docs#curl/introduction doc ID is appAaBbCcDdEeFf

Copy Airtable API key. Click show API key checkbox and scroll down to Authentication section.

Create account at Sendgrid.com, create and copy API key

Create account at Intercom, copy API key


Edit credentials file
	
	cd /var/www/admin_include
	cp env_sample.php env.php
	nano env.php
	
Enter your keys and parameters. Save
