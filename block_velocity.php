<?php
require_once($CFG->libdir . '/completionlib.php');
require_once('velocity_form.php');

class block_velocity extends block_base
{
    public function init()
    {
        $this->title = get_string('velocity', 'block_velocity');
    }

    public function get_content()
    {
        global $OUTPUT;
        global $DB;
        global $USER;
        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $userid = $USER->id;
        $year = array();
        $sixmon = array();
        $threemon = array();
        $onemon = array();
        $week = array();
        $times = array();
        $today = time(); //date('l\, j F Y h:i A');
        $message = "";
        $option = "";
        $courses = array();
        $options = array('year' => '1 Year', 'sixmonths' => '6 Months', 'threemonths' => '3 Months', 'onemonth' => '1 Month', 'week' => '1 Week');
        $result = $DB->get_records_sql('SELECT id, course, timecompleted FROM {course_completions} WHERE userid = ? AND timecompleted > 0', array($userid));
        if (!$result) {
            $this->content->text = "You have no completed courses.";
            return $this->content;
        }

        foreach ($result as $record) {
            $course = $DB->get_record('course', array('id' => $record->course));
            /*$info = new completion_info($course);
            $completion = $info->get_completions($USER->id);*/
            $time = $record->timecompleted;

            array_push($times, $time);
            $datediff = floor(($today - $time) / DAYSECS);

            $course_name = $course->fullname;
            if ($datediff < 7) {
                array_push($week, $course_name);
                array_push($onemon, $course_name);
                array_push($threemon, $course_name);
                array_push($sixmon, $course_name);
                array_push($year, $course_name);
            } else if ($datediff < 31) {
                array_push($onemon, $course_name);
                array_push($threemon, $course_name);
                array_push($sixmon, $course_name);
                array_push($year, $course_name);
            } else if ($datediff < 93) {
                array_push($threemon, $course_name);
                array_push($sixmon, $course_name);
                array_push($year, $course_name);
            } else if ($datediff < 186) {
                array_push($sixmon, $course_name);
                array_push($year, $course_name);
            } else if ($datediff < 365) {
                array_push($year, $course_name);
            }
        }

        $url = new moodle_url('', array('id' => $COURSE->id));
        $option = optional_param('velocity_time', 'year', PARAM_TEXT);
        $choice = $options['year'];
        if ($option) {
            $choice = $option;
        }
        $select = new single_select($url, 'velocity_time', $options, $choice, array());
        switch ($option) {
            case "year":
                $courses = $year;
                break;
            case "sixmonths" :
                $courses = $sixmon;
                break;
            case "threemonths" :
                $courses = $threemon;
                break;
            case "onemonth" :
                $courses = $onemon;
                break;
            case "week" :
                $courses = $week;
        }

        if (empty($courses)) {
            $message = "No Courses completed in given period of time";
        } else {

            foreach ($courses as $c) {
                $message = $message . $c . "<br/>";
            }
        }
        $this->content->text = "Courses completed in the last <br/>" . $OUTPUT->render($select) . $message;

        return $this->content;
    }
}
