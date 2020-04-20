# MAG Portal

<!-- wp:paragraph -->
<p>The Mutual Aid Groups (MAG) Administration Portal allows group admins to manage their own group's information, and only their own group's information, in the MANYC Groups Airtable database. </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Some of this data is published in the <a href="http://mutualaid.nyc/groups">map and resources</a> sections of the mutualaid.nyc website and used by our organizing team to keep groups up to date and organized. </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Users can CRUD three tables of information in the MANYC Groups database:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Groups: This is the profile information of their group(s), including but not limited to the data that is displayed on the MANYC Groups map.</li><li>Users: This is the profile information of the individual user, such as their name, email and phone.</li><li>Requests: This is a table of Requests the group wants to share with the MANYC network. Currently we've implemented a form whereby groups can request volunteers.</li></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Users can access the tool at https://mutualaid.nyc/manage-your-group/.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>From there, users submit their email address. An email is then sent to them that contains a magic link that logs them into the system. From there they can create groups, edit groups that they "own", update their individual profile information and submit and track requests.</p>
<!-- /wp:paragraph -->


## Installation

By default it is supposed that your server root folder is /var/www/html

    cd /var/www
	git clone https://github.com/sarapis/MAGportal.git
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
