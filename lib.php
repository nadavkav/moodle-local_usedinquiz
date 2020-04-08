<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/question/editlib.php');

function local_usedinquiz_get_question_bank_search_conditions($caller) {
    return array( new local_usedinquiz_question_bank_search_condition($caller));
}

class local_usedinquiz_question_bank_search_condition extends core_question\bank\search\condition  {
    protected $where;
    protected $params;

    const ALLQUIZZES = 0;

    public function __construct() {
        $this->onlyquizid = optional_param('onlyquizid', 0, PARAM_INT);

        if ($this->onlyquizid != self::ALLQUIZZES) {
            $this->init();
        }
    }

    public function where() {
        return $this->where;
    }

    public function params() {
        return $this->params;
    }

    public function display_options_adv() {
        global $DB, $COURSE;
        echo "<br />\n";

        $coursequizzes = $DB->get_records('quiz', ['course' => $COURSE->id]);
        foreach ($coursequizzes as $quiz) {
            $options[$quiz->id] = get_string('usedinquiz', 'local_usedinquiz', $quiz->name);
        }
        $attr = array ('class' => 'searchoptions');
        if (!empty($options)) {
            echo html_writer::select($options, 'onlyquizid', $this->onlyquizid,
                array(self::ALLQUIZZES => get_string('allquizzes', 'local_usedinquiz')), $attr);
        }

    }

    private function init() {
        if ($this->onlyquizid != self::ALLQUIZZES) {
            $this->where = '(q.id IN (SELECT questionid FROM {quiz_slots} WHERE quizid = '.$this->onlyquizid.'))';
        } else {
            $this->where = '(q.id NOT IN (SELECT questionid FROM {quiz_slots}))';
        }
    }

}
