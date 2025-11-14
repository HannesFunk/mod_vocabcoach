<?php
namespace mod_vocabcoach\output;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../box_manager.php');
// require_once(__DIR__ . '/../activity_tracker.php');

use mod_vocabcoach\box_manager;
//use mod_vocabcoach\activity_tracker;

class mobile {
    
    /**
     * Returns the main mobile view for the vocabulary coach module.
     * 
     * @param array $args Arguments from mobile app
     * @return array Mobile view data
     */
    public static function mobile_view(array $args) : array {
        global $OUTPUT, $USER, $DB, $CFG;
        
        xdebug_break(); // Force Xdebug to break here
        
        // Add debugging output
        error_log('🔍 MOBILE_VIEW: mobile_view called with args: ' . json_encode($args));
        error_log('🔍 MOBILE_VIEW: $_GET params: ' . json_encode($_GET));
        error_log('🔍 MOBILE_VIEW: $_REQUEST params: ' . json_encode($_REQUEST));
        
        $cmid = $args['cmid'];
        $courseid = $args['courseid'];
        
        // Debug mode for testing
        $debug = isset($args['debug']) ? $args['debug'] : false;
        if ($debug) {
            error_log('VocabCoach Mobile Debug: ' . json_encode($args));
        }
        
        // Check for stage/action in args OR in GET/REQUEST parameters
        $stage = $args['stage'] ?? $_GET['stage'] ?? $_REQUEST['stage'] ?? null;
        $action = $args['action'] ?? $_GET['action'] ?? $_REQUEST['action'] ?? null;
        
        // Check if this is a check request
        if (($stage && $stage > 0) || ($action === 'check')) {
            error_log('🔍 MOBILE_VIEW: Redirecting to mobile_check with stage=' . $stage);
            $args['stage'] = $stage;
            $args['action'] = $action;
            return self::mobile_check($args);
        }
        
        error_log(print_r("🔍 MOBILE".$args, true));
        // Get module instance data
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
        
        // Get box data using the box manager
        $boxmanager = new box_manager($cmid, $USER->id);
        $boxdata = $boxmanager->get_box_details();
        
        error_log('🔍 MOBILE_VIEW: Box data retrieved: ' . json_encode($boxdata));
        
        // Pre-load vocab data for all stages so JavaScript can access it
        $checkapi = new \mod_vocabcoach\external\vocab_api();
        $vocabDataAllStages = [];
        foreach ($boxdata as $box) {
            if ($box['due'] > 0) {
                $vocabDataAllStages[$box['stage']] = $checkapi->get_user_vocabs($USER->id, $cmid, $box['stage'], false);
            }
        }
        
        error_log('🔍 MOBILE_VIEW: Vocab data loaded for stages: ' . json_encode(array_keys($vocabDataAllStages)));
        
        // Activity tracking
        // $al = new activity_tracker($USER->id, $cmid);
        // $al->log($al->typesdaily['ACT_LOGGED_IN']);
        // if ($al->is_all_done($boxdata)) {
        //     $al->log($al->typesdaily['ACT_CHECKED_ALL']);
        // }
        
        $templatecontext = [
            'boxdata' => json_encode($boxdata), // Convert array to JSON string
            'cmid' => $cmid,
            'courseid' => $courseid,
            'userid' => $USER->id,
           // 'days_logged_in' => $al->get_continuous_days($al->typesdaily['ACT_LOGGED_IN']),
           // 'days_checked_all' => $al->get_continuous_days($al->typesdaily['ACT_CHECKED_ALL']),
            'modulename' => $moduleinstance->name,
            'wwwroot' => $CFG->wwwroot ?? '',
            'vocabdata' => json_encode($vocabDataAllStages), // Add vocab data for JavaScript
        ];
        
        $javascript = '';
        $debugInfo = '';
        try {
            $jsPath = $CFG->dirroot . '/mod/vocabcoach/mobileapp/mobile.js';
            $javascript = file_get_contents($jsPath);
            $debugInfo = "Successfully loaded JS from: " . $jsPath;
        } catch (\Exception $e) {
            $debugInfo = "Error loading JS: " . $e->getMessage();
            error_log('🔍 MOBILE_VIEW: Error loading JavaScript: ' . $e->getMessage());
        }

        $result = [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_vocabcoach/mobile/mobile_main', [
                        'boxdata' => $boxdata, // Use actual arrays here!
                        'cmid' => $cmid,
                        'courseid' => $courseid,
                        'userid' => $USER->id,
                        'modulename' => $moduleinstance->name,
                        'wwwroot' => $CFG->wwwroot ?? '',
                        'debug-info' => $debugInfo,
                    ]),
                ],
            ],
            'javascript' => $javascript,
            'otherdata' => [
                // Only scalar values here
                'cmid' => $cmid,
                'courseid' => $courseid,
                'userid' => $USER->id,
                'modulename' => $moduleinstance->name,
                'wwwroot' => $CFG->wwwroot ?? '',
            ],
        ];
        return $result;
    }
    
    /**
     * Returns the mobile check view for vocabulary checking.
     * 
     * @param array $args Arguments from mobile app  
     * @return array Mobile view data
     */
    public static function mobile_check(array $args) : array {
        global $OUTPUT, $USER, $DB;
        
        error_log('🔍 MOBILE_CHECK: mobile_check called with args: ' . json_encode($args));
        
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
            'vocabdata' => json_encode($vocabarray), // Already JSON encoded
            'config' => json_encode($config), // Already JSON encoded
            'cmid' => $cmid,
            'stage' => $stage,
            'userid' => $USER->id,
            'hasVocab' => !empty($vocabarray) ? 1 : 0, // Convert boolean to int
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
            'javascript' => file_get_contents($CFG->dirroot . '/mod/vocabcoach/mobileapp/mobile_check.js'),
            'otherdata' => $templatecontext,
        ];
    }
}
