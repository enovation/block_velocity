<?php

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
class velocity_form extends moodleform {
    //Add elements to form
    function definition() {
        global $CFG;
 		$TIMES = array('year'=>'1 Year', 'sixmonths'=>'6 Months', 'threemonths'=>'3 Months', 'onemonth'=>'1 Month', 'week'=>'1 Week' );
        $mform =& $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('select', 'time', '', $TIMES); // Add elements to your form
        $mform->setType('time', PARAM_TEXT);                   //Set type of element
        $mform->setDefault('time', 'year');        //Default value
             }

	    public function html() {
        return $this->_form->toHtml();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
