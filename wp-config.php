<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wp3');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$rXibs_w~n#D9f{LFHfqH3Z1djAh1]C!X%`^pP^yTO4/#=5-xfpDO4t&HEzD+kRe');
define('SECURE_AUTH_KEY',  '[1`Q4:2`{XKtXiN*7nvvQ>o57iM+/_JX/a+[;+~kXkc-@R ;+j:7g3K^N*/E&AJ.');
define('LOGGED_IN_KEY',    '&s1UKFZ#*^ys8&3^p!,>8S?Mi302/Do$Dxei$-) R$Vk+;|&GR.$s:YX_o8IyO?3');
define('NONCE_KEY',        '0HJ#09(sF:v$l#fiGj5H 3~#ZbU0.hw825,~t@SNMp(.<y<n]PD)s*nd{fyB^vL5');
define('AUTH_SALT',        'N!(O@!{5tkEQ:Byx#pT[!$Y@|0q(fj:.x{gw~<L?PdX2+0fg3zw/0W#KJHs<1lMm');
define('SECURE_AUTH_SALT', 'VL<Mn3Am UB&Uhi1~{QY4*P;sTc|&$%.FZkge-MpElM/dsZ#IjGazH8:7rqL/~M)');
define('LOGGED_IN_SALT',   'PxgKo!,I,j`6vMe+<7&E/|cKA_FL82Pg>R9?]op!CP 0T]}X=O2Tn94mIlw3~Y.k');
define('NONCE_SALT',       '&m-Ct;!k(r7I1o:W$9H/`$ZE1MV)6F`1{)UYPE{1POa2HAK7z71a1&,p4r}dF|mW');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
