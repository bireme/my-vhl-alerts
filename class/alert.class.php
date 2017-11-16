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
     * @return string
     */
    public static function getUnsubscribeLink($token){
        $home = base64_encode(MY_VHL_DOMAIN);
        $token = urlencode($token);
        $url = MY_VHL_DOMAIN."/server/pub/userData.php?c=".$home."&ut=".$token."&acao=alertas";

        return $url;
    }

    /**
     * Make user email template
     *
     * @param string $id User ID
     * @return string User email template
     */
    public static function make_template($id){
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
                    $title = SimDocs::get_similardoc_title($similar);

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

        if ( $topicsTemplates ) {
            $token = makeUserTK($user['userID'],$user['sysUID']);
            $unsubscribe_link = self::getUnsubscribeLink($token);

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
            $layout->set("title", "Minha BVS - Alertas");
            $layout->set("stylesheet", $stylesheet->output());
            $layout->set("content", $topicsContents);
            $layout->set("unsubscribe_link", $unsubscribe_link);
            
            /**
             * Output page.
             */
            echo $layout->output();
        } else {
            echo "Nenhum documento encontrado.";
        }
    }

}
?>