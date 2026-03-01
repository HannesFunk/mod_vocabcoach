<?php


namespace mod_vocabcoach;
use invalid_parameter_exception;

class streak_manager
{
    /**
     * @var int $userid User ID
     * @var int $cmid Course Module ID
     */
    private int $userid, $cmid;
    private array $types = ['login', 'checkall'];

    /**
     * Construct the class.
     * @param int $userid User id
     * @param int $cmid Course module id
     */
    public function __construct(int $userid, int $cmid)
    {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    /**
     * Get the current streak info of the user.
     * @return object Current streak
     */
    public function get_streak(string $type): object
    {
        global $DB;
        $record = $DB->get_record(
            'vocabcoach_streaks',
            ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
        );

        if (!$record) {
            return (object)[
                'userid' => $this->userid,
                'cmid' => $this->cmid,
                'type' => $type,
                'streak' => 1,
            ];
        }
        return $record;
    }


    public function get_streak_info($selectedtype = null): object
    {
        $info = [];
        if ($selectedtype && !in_array($selectedtype, $this->types)) {
            throw new \core\exception\invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        $types = $selectedtype ? [$selectedtype] : $this->types;
        foreach ($types as $type) {
            $streak = $this->get_streak($type);
            $info[$type]['streak'] = $streak->streak;
//            $info[$type]['status'] = $streak->status;
        }
        if ($selectedtype) {
            return (object)$info[$selectedtype];
        } else {
            return (object)$info;
        }
    }

    public function update(): void
    {
        foreach ($this->types as $type) {
            $this->update_type($type);
        }
    }

    public function update_type(string $type): void
    {
        if (!in_array($type, $this->types)) {
            throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        global $DB;
        $streak = $this->get_streak($type);

        // Streak can no longer be restored
        if ($streak->lastmodified < strtotime("-2 days midnight")) {
            $streak->lastmodified = time();
            $streak->streak = 1;
            $DB->update_record('vocabcoach_streaks', $streak);
        } // streak has not yet been updated and doesn't need to be restored
        else if ($streak->lastmodified < strtotime("today midnight")) {
            $streak->lastmodified = time();
            $streak->streak++;
            $DB->update_record('vocabcoach_streaks', $streak);
        }
    }
}