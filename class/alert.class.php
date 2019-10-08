<?php
/**
 * Alerts Class
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

require_once __DIR__ . '/user.class.php';
require_once __DIR__ . '/profile.class.php';
require_once __DIR__ . '/simdocs.class.php';
require_once __DIR__ . '/template.class.php';

class Alerts {

    public function __construct() {}

    /**
     * Check if user is valid
     *
     * @param array $user User data
     * @return bool
     */
    public static function is_valid($user){
        $count = 0;

        $profiles = Profiles::get_profiles_list($user['sysUID']);
        
        if ( !$profiles ) {
            return FALSE;
        }

        foreach ($profiles as $profile) {
            $similars = SimDocs::get_similars($user['userID'], $profile['profileName']);

            if ( $similars ) {
                $count++;
            }
        }

        if ( $count == 0 ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get unsubscribe link
     *
     * @param string $token User token
     * @param string $lang Template language
     * @return string
     */
    public static function getUnsubscribeLink($token, $lang){
        $home = base64_encode(MY_VHL_DOMAIN);
        $token = urlencode($token);
        $url = MY_VHL_DOMAIN."/server/pub/userData.php?c=".$home."&ut=".$token."&acao=alertas&lang=".$lang;

        return $url;
    }

    /**
     * Get regards text
     *
     * @param string $gender User gender
     * @param string $text Regards text
     * @return string
     */
    public static function getRegards($gender, $text){
        $text = explode('|', $text);

        $regards = array();
        $regards['M'] = $text[0];
        $regards['F'] = $text[1];

        return $regards[$gender];
    }

    /**
     * Make user email template
     *
     * @param string $id User ID
     * @param string $lang Template language
     * @return string User email template
     */
    public static function make_template($id, $lang){
        /**
         * Defines an array for the topics templates.
         */
        $topicsTemplates = array();

        /**
         * Defines an array for the user data.
         */
        $user = Users::get_user_data($id);

        if ( !$user ) {
            die("Usuário não encontrado.");
        }

        /**
         * Defines an array for the user profiles.
         */
        $profiles = Profiles::get_profiles_list($user['sysUID']);

        if ( !$profiles ) {
            die("Nenhum tema encontrado.");
        }

        /**
         * Loop through the profiles and creates a template for each one.
         */
        foreach ($profiles as $profile) {
            /**
             * Defines an array for the new docs templates.
             */
            $docsTemplates = array();

            /**
             * Defines an array for the new similar.
             */
            $similars = SimDocs::get_similars($user['userID'], $profile['profileName']);

            if ( $similars ) {
                $string = '';

                /**
                 * Loop through the users and creates a template for each one.
                 */
                foreach ($similars as $index => $similar) {
                    if ( !$similar['total'] ) {
                        $title = SimDocs::get_similardoc_title($similar, $lang);

                        if ( !empty($string) ) {
                            if ( strtolower(rtrim($title, '.')) == strtolower(rtrim($string, '.')) ) {
                                continue;
                            }
                        }

                        $url = SimDocs::generate_similardoc_url($similar['id']);

                        $docs = new Template("alerts_docs.tpl");
                        $docs->set("title", $title);
                        $docs->set("url", $url);

                        $docsTemplates[] = $docs;

                        $string = $title;
                    }
                }

                if ( $docsTemplates ) {
                    /**
                     * Merges all our docs templates into a single variable.
                     */
                    $docsContents = Template::merge($docsTemplates);

                    $topic = new Template("alerts_topics.tpl");
                    $topic->set("topic", $profile['profileName']);
                    $topic->set("docs", $docsContents);

                    $topicsTemplates[] = $topic;
                }
            }
        }

        if ( $topicsTemplates ) {
            $token = makeUserTK($user['userID'],$user['sysUID']);
            $unsubscribe_link = self::getUnsubscribeLink($token, $lang);
            $texts = parse_ini_file("../ini/".$lang."/texts.ini");
            $regards = self::getRegards($user['userGender'], $texts['REGARDS']);

            /**
             * Loads stylesheet template.
             */
            $stylesheet = new Template("style.css");

            /**
             * Merges all our topics templates into a single variable.
             */
            $topicsContents = Template::merge($topicsTemplates);

            /**
             * Loads main template, settings its title and content.
             */
            $layout = new Template("alerts.tpl");
            $layout->set("lang", $lang);
            $layout->set("title", $texts['TITLE']);
            $layout->set("subtitle", $texts['SUBTITLE']);
            $layout->set("header", $texts['HEADER']);
            $layout->set("regards", $regards);
            $layout->set("user", $user['userFirstName']);
            $layout->set("description", $texts['DESCRIPTION']);
            $layout->set("unsubscribe", $texts['UNSUBSCRIBE']);
            $layout->set("att", $texts['ATT']);
            $layout->set("signature", $texts['SIGNATURE']);
            $layout->set("footer", $texts['FOOTER']);
            $layout->set("feedback", $texts['FEEDBACK']);
            $layout->set("stylesheet", $stylesheet->output());
            $layout->set("content", $topicsContents);
            $layout->set("unsubscribe_link", $unsubscribe_link);
            
            /**
             * Output page.
             */
            echo $layout->output();
        } else {
            $token = makeUserTK($user['userID'],$user['sysUID']);
            $unsubscribe_link = self::getUnsubscribeLink($token, $lang);
            $texts = parse_ini_file("../ini/".$lang."/texts.ini");
            $regards = self::getRegards($user['userGender'], $texts['REGARDS']);

            /**
             * Loads stylesheet template.
             */
            $stylesheet = new Template("style.css");

            /**
             * Loads main template, settings its title and content.
             */
            $layout = new Template("alerts_error.tpl");
            $layout->set("lang", $lang);
            $layout->set("title", $texts['TITLE']);
            $layout->set("subtitle", $texts['SUBTITLE']);
            $layout->set("header", $texts['HEADER']);
            $layout->set("regards", $regards);
            $layout->set("user", $user['userFirstName']);
            $layout->set("description", $texts['DESCRIPTION']);
            $layout->set("unsubscribe", $texts['UNSUBSCRIBE']);
            $layout->set("att", $texts['ATT']);
            $layout->set("signature", $texts['SIGNATURE']);
            $layout->set("footer", $texts['FOOTER']);
            $layout->set("feedback", $texts['FEEDBACK']);
            $layout->set("stylesheet", $stylesheet->output());
            $layout->set("content", $texts['ERROR']);
            $layout->set("url", MY_VHL_DOMAIN);
            $layout->set("unsubscribe_link", $unsubscribe_link);
            
            /**
             * Output page.
             */
            echo $layout->output();
        }
    }

}
?>