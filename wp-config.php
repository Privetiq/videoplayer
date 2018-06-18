<?php


// ** ������������ MySQL **  //Added by WP-Cache Manager
 //Added by WP-Cache Manager
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/www/clients/client3/web43/web/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'c3_pravonavladu');    // ����� ���� ��
define('DB_USER', 'root');     // Your MySQL ��� �����������
define('DB_PASSWORD', ''); // ��� ������
define('DB_HOST', '127.0.0.1');    // 99 ������� �� ��� �� ����� �������� ��
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('DISALLOW_FILE_EDIT', true);
// �� ������ ���� ������� ���������� WP � ���� ��� �����,
// ���� ���������� ����� ��� ��������
$table_prefix  = 'wp_';   // ҳ���� �������� ����� �� �������������!

// �������� � ���������� WP. ��� �����������, ��� ��������� ����
// ��������� .mo ���� � ���� wp-content/languages
// ��������� ��������� ru_RU.mo �� wp-content/languages �� 
// ��������� WPLANG � 'ru_RU' ��� ��������������� �������� ����.
define ('WPLANG', 'uk'); // ��������� �� �������������
define('SCRIPT_DEBUG', true);
/* �� ���, ������� ���������� � ������������ �� ������ ������! */
//define('SAVEQUERIES', true);
define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
