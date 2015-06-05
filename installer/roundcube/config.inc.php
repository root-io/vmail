<?php

$config = array();

$config['db_dsnw'] = 'mysql://roundcube:CONFIG_MARIADB_ROUNDCUBE_PASSWORD@localhost/roundcube';
$config['log_logins'] = true;
$config['default_host'] = 'ssl://CONFIG_DOMAIN';
$config['default_port'] = 993;
$config['imap_auth_type'] = 'PLAIN';
$config['imap_delimiter'] = '.';
$config['smtp_server'] = 'ssl://CONFIG_DOMAIN';
$config['smtp_port'] = 465;
$config['smtp_user'] = '%u';
$config['smtp_pass'] = '%p';
$config['smtp_auth_type'] = 'PLAIN';
$config['session_domain'] = '.CONFIG_DOMAIN';
$config['password_charset'] = 'UTF-8';
$config['sendmail_delay'] = 20;
$config['useragent'] = 'Roundcube Webmail/';
$config['identities_level'] = 1;
$config['spellcheck_dictionary'] = false;
$config['default_charset'] = 'UTF-8';
$config['refresh_interval'] = 300;
$config['des_key'] = 'SECRET';
$config['create_default_folders'] = true;

$config['imap_conn_options'] = array(
  'ssl'         => array(
	 'verify_peer'  => false,
	 'verfify_peer_name' => false,
   ),
);

$config['smtp_conn_options'] = array(
  'ssl'         => array(
	 'verify_peer'  => false,
	 'verfify_peer_name' => false,
   ),
);
