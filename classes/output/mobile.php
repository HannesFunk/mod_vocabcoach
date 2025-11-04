<?php
namespace mod_vocabcoach\output;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../box_manager.php');
require_once(__DIR__ . '/../activity_tracker.php');

use mod_vocabcoach\box_manager;
use mod_vocabcoach\activity_tracker;

class mobile {
    
    /**
     * Returns the main mobile view for the vocabulary coach module.
     * 
     * @param array $args Arguments from mobile app
     * @return array Mobile view data
     */
    public static function mobile_view(array $args) : array {
        global $OUTPUT, $USER, $DB, $CFG;
        
        $cmid = $args['cmid'];
        $courseid = $args['courseid'];
        
        // Debug mode for testing
        $debug = isset($args['debug']) ? $args['debug'] : false;
        if ($debug) {
            error_log('VocabCoach Mobile Debug: ' . json_encode($args));
        }
        
        // Check if this is a check request
        if ((isset($args['stage']) && $args['stage'] > 0) || (isset($args['action']) && $args['action'] === 'check')) {
            return self::mobile_check($args);
        }
        
        // Get module instance data
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
        
        // Get box data using the box manager
        $boxmanager = new box_manager($cmid, $USER->id);
        $boxdata = $boxmanager->get_box_details();
        
        // Activity tracking
        $al = new activity_tracker($USER->id, $cmid);
        $al->log($al->typesdaily['ACT_LOGGED_IN']);
        if ($al->is_all_done($boxdata)) {
            $al->log($al->typesdaily['ACT_CHECKED_ALL']);
        }
        
        $templatecontext = [
            'boxdata' => $boxdata,
            'cmid' => $cmid,
            'courseid' => $courseid,
            'userid' => $USER->id,
            'days_logged_in' => $al->get_continuous_days($al->typesdaily['ACT_LOGGED_IN']),
            'days_checked_all' => $al->get_continuous_days($al->typesdaily['ACT_CHECKED_ALL']),
            'modulename' => $moduleinstance->name,
            'wwwroot' => $CFG->wwwroot ?? '',
        ];
        
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<h1 class="text-center">Hallo, ballo!</h1>',
                ],
            ],
            'javascript' => '',
            'otherdata' => $templatecontext,
        ];
    }
    
    /**
     * Returns the mobile check view for vocabulary checking.
     * 
     * @param array $args Arguments from mobile app  
     * @return array Mobile view data
     */
    public static function mobile_check(array $args) : array {
        global $OUTPUT, $USER, $DB;
        
        $cmid = $args['cmid'];
        $stage = isset($args['stage']) ? (int)$args['stage'] : 1;
        $force = isset($args['force']) ? (bool)$args['force'] : false;
        
        // Get module instance data
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
        
        // Get vocabulary for checking
        $checkapi = new \mod_vocabcoach\external\vocab_api();
        $vocabarray = $checkapi->get_user_vocabs($USER->id, $cmid, $stage, $force);
        
        $config = [
            'source' => 'user',
            'userid' => $USER->id,
            'cmid' => $cmid,
            'stage' => $stage,
            'force' => $force,
            'move_undue' => $moduleinstance->move_undue,
            'third_active' => $moduleinstance->thirdactive,
        ];
        
        $templatecontext = [
            'vocabdata' => json_encode($vocabarray),
            'config' => json_encode($config),
            'cmid' => $cmid,
            'stage' => $stage,
            'userid' => $USER->id,
            'hasVocab' => !empty($vocabarray),
            'vocabCount' => count($vocabarray),
            'modulename' => $moduleinstance->name,
        ];
        
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_vocabcoach/mobile_check', $templatecontext),
                ],
            ],
            'javascript' => 'require(["mod_vocabcoach/mobile_check"], function(mobileCheck) { 
                mobileCheck.mobileCheckInit(' . json_encode($config) . '); 
            });',
            'otherdata' => $templatecontext,
        ];
    }
}
