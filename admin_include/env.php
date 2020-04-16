<?php
define('HOST', 'https://group-mgmt.mutualaid.nyc');

define('ADMIN_SESSION_VERBOSE', false);


////// MAG PORTAL APP /////////////////////////////////////////////////////////////////////////////////////////////////////////

define('SENDGRID_API_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('ADMIN_AUTH_EMAIL', 'authbot@wegov.nyc');
define('ADMIN_AUTH_SENDER', 'Auth Bot');

define('ADMIN_AIRTABLE_KEY', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
//define('ADMIN_AIRTABLE_DOC', 'appt10gfQj2fn0mLB');	// test
define('ADMIN_AIRTABLE_DOC', 'appF8rJZ3XAkzWOO9');	// wrk
define('ADMIN_AIRTABLE_USR', 'users');
define('ADMIN_AIRTABLE_GR', 'Groups');
define('ADMIN_AIRTABLE_POSTS', 'Requests');
define('ADMIN_FILES_DIR', 'adminfiles');
define('ADMIN_SESSION_DURATION_LIM', 7200);

define('INTERCOM_KEY', 'wib83ryk');



$AdminUserFormFields = [		// input name => airtable field name
	'email' => 'Email',			// auth email field [input name] should remain 'email'
	'first_name' => 'First Name',
	'last_name' => 'Last Name',
	'phone' => 'Phone',
	'group_id' => 'Groups',
	'group_name' => 'Group Name',
	'group_last_modified' => 'Group Last Modified',
	'group_posts_num' => 'Request Count',
	'registered_at' => 'Registered At',
	'is_new' => 'is_new',
];


$AdminGrFormFields = [		// input name => airtable field name
	'name' => 'Group Name',	
	'description' => 'Short Description',
	'email' => 'Group Email',
	'website' => 'Website',
	'phone' => 'Group Phone',
	'neighborhoods' => 'Neighborhoods',
	'geoscope' => 'Geographical Scope',
	'coverimg' => 'Cover Image',
	'facebook' => 'Facebook',
	'instagram' => 'Instagram',
	'twitter' => 'Twitter',
	'user_id' => 'user',
	'id' => 'id',
];

$AdminFormStaticSelects = [
	'geoscope' => ['Global','National','New York State','New York City', 'Bronx','Brooklyn','Manhattan','Long Island','New Jersey','Queens','Staten Island'],
];

$PostsFields = [		// input name => airtable field name
	'label' => 'MAG Label',
	'group' => 'Group',
	'text' => 'Text for Volunteer Blast',
	'link' => 'Register Link',
	'address' => 'Opportunity Address',
	'location' => 'Other Location Information',
	'start' => 'Opportunity Start Date',
	'end' => 'Opportunity End Date',
	'types' => 'Task Types',
	'created_at' => 'Created Time',
	'modified_at' => 'Status Last Modified',
	'status' => 'Status',
	'user' => 'user',
	'id' => 'id',
];

$PostFormStaticSelects = [
	'types' => ['Picking up groceries', 'Picking up a prescription', '1 on 1 check-ins (phone call, Zoom, etc to touch base with a neighbor)', 'Translation and interpretation in a language other than English', 'Serve as local coordinator to link folks near me', 'Social services guidance (filing for medicare, unemployment, etc)', 'Moving things with a vehicle'],
];
