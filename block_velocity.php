<?php
require_once($CFG->libdir.'/completionlib.php');
require_once('velocity_form.php');

class block_velocity extends block_base{

    public function init(){
		$this->title = get_string('velocity', 'block_velocity');
    }
	
    public function get_content(){
                
    global $OUTPUT;
    global $DB;
	if($this->content !== null){
            return $this->content;
	} else {
            global $USER;
            $userid = $USER->id;
            $year = array();
            $sixmon = array();
            $threemon = array();
            $onemon = array();
            $week = array();
            $times = array();
            $today = date('l\, j F Y h:i A');
            $message = "";
            $option="";
            $courses = array();
            $options = array('year'=>'1 Year', 'sixmonths'=>'6 Months', 'threemonths'=>'3 Months', 'onemonth'=>'1 Month', 'week'=>'1 Week' );
		$result = $DB->get_records_sql('SELECT id, course, timecompleted FROM {course_completions} WHERE userid = ?', array($userid));
                    if($result){
			foreach($result as $record){
                            $course = $DB->get_record('course', array('id'=>$record->course));
                            $info = new completion_info($course);
                            $completion = $info->get_completion($USER->id, COMPLETION_CRITERIA_TYPE_SELF);
                            $time = userdate($completion->timecompleted);
                            array_push($times, $time);
                            $datediff = floor(((strtotime($today)) - (strtotime($time)))/DAYSECS);
                            $course_name = $course->fullname;                                       
				if($datediff < 7){
                                    array_push($week, $course_name); 
				}else if($datediff < 31){
                                    array_push($onemon, $course_name);
				}else if($datediff < 93){
                                    array_push($threemon, $course_name);
				}else if($datediff < 186){
                                    array_push($sixmon, $course_name);
				}else if($datediff < 365){
                                    array_push($year, $course_name);
				} else {
                                    $messsage = "No Courses completed in given period of time";
				}
			}
				
                        $url = new moodle_url('/my/index.php');
                        $option = optional_param('time', -1, PARAM_TEXT);
                        $choice = $options['year'];
                        if($option){
                            $choice = $option;
                        }
                        $select = new single_select($url, 'time', $options, $choice, array());
                        switch($option){
                            case "year": $courses = $year;
                                break;
                            case "sixmonths" : $courses = $sixmon;
                                break;
                            case "threemonths" : $courses = $threemon;
                                break;
                            case "onemonth" : $courses = $onemon;
                                break;
                            case "week" : $courses = $week;
                        }

                        if(empty($courses)){
                            $message = "No Courses completed in given period of time";
                        }else{
                        
                            foreach($courses as $c){
                                $message = $message . $c . "<br/>";
                            }
                        }
                        
                    }
		
		$this->content = new stdClass;
			
		$this->content->text = "Courses completed in the last <br/>" . $OUTPUT->render($select) . $message;

		return $this->content;
		}
	}
}
