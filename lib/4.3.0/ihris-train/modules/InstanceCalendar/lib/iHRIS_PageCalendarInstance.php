<?php
/**
* © Copyright 2013 IntraHealth International, Inc.
* 
* This File is part of I2CE 
* 
* I2CE is free software; you can redistribute it and/or modify 
* it under the terms of the GNU General Public License as published by 
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License 
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
* @package iHRIS
* @subpackage Common
* @author Luke Duncan <lduncan@intrahealth.org>
* @version v4.1.6
* @since v4.1.6
* @filesource 
*/ 
/** 
* Class I2CE_PageCalendarInstance
* 
* @access public
*/


class iHRIS_PageCalendarInstance extends iHRIS_PageCalendar {

    /**
     * Return the href for this page.
     * @return string
     */
    protected function getHref() {
        return 'instance_calendar';
    }
    
    /**
     * Perform the actions of this page.
     * @return boolean
     */
    protected function action() {
        if ( parent::action() ) {
            $next_month = getdate( mktime( 0, 0, 0, $this->month + 1, 1, $this->year ) );
            $db_start = sprintf( '%04d-%02d-%02d', $this->year, $this->month, 1 );
            $db_end = sprintf( '%04d-%02d-%02d', $next_month['year'], $next_month['mon'], $next_month['mday'] );
            $find_instances = array(
                    'operator' => 'AND',
                    'operand' => array(
                        0 => array(
                            'operator' => 'OR',
                            'operand' => array(
                                0 => array(
                                    'operator' => 'AND',
                                    'operand' => array(
                                        0 => array(
                                            'operator' => 'FIELD_LIMIT',
                                            'style' => 'greaterthan_equals',
                                            'field' => 'start_date',
                                            'data' => array( 'value' => $db_start ),
                                            ),
                                        1 => array(
                                            'operator' => 'FIELD_LIMIT',
                                            'style' => 'lessthan',
                                            'field' => 'start_date',
                                            'data' => array( 'value' => $db_end ),
                                            ),
                                        ),
                                    ),
                                1 => array(
                                    'operator' => 'AND',
                                    'operand' => array(
                                        0 => array(
                                            'operator' => 'FIELD_LIMIT',
                                            'style' => 'greaterthan_equals',
                                            'field' => 'end_date',
                                            'data' => array( 'value' => $db_start ),
                                            ),
								        1 => array(
								            'operator' => 'FIELD_LIMIT',
								            'style' => 'lessthan',
                                            'field' => 'end_date',
                                            'data' => array( 'value' => $db_end),
									        ),
                                        ),
                                    ),
                                ),
                            ),
                        1 => array(
                            'operator' => 'OR',
                            'operand' => array(
                                0 => array(
                                    'operator' => 'FIELD_LIMIT',
                                    'style' => 'null',
                                    'field' => 'cancelled',
                                    ),
                                1 => array(
                                    'operator' => 'FIELD_LIMIT',
                                    'style' => 'no',
                                    'field' => 'cancelled',
                                    ),
                                ),
                            ),
                        ),
                    );
            $instances = I2CE_FormStorage::search( "provider_instance", false, $find_instances );
            $factory = I2CE_FormFactory::instance();
            $month_end = new DateTime( $db_end );
            $deviation = 35;
            $base = array( 70, 116, 149 );
            foreach( $instances as $instance ) {
                $rgb = array();
                foreach( $base as $idx => $num ) {
                    $rgb[$idx] = rand( $num-$deviation, $num+$deviation );
                }
                $inst = $factory->createContainer( "provider_instance|$instance" );
                $inst->populate();


                $this->addForm( $inst, $inst->start_date->getDateTimeObj(), $inst->end_date->getDateTimeObj(),
                        'calendar_provider_instance_day.html', $rgb );
            }
                    
        } else {
            return false;
        }
        return true;
    }

    /**
     * Set the active menu for this page.
     */
    protected function setActiveMenu() {
        $this->template->setAttribute("class", "active", "menu_trainingprovider", "a[@href='manage?action=provider']" );
        $this->template->appendFileById( "menu_manage_provider.html", "ul", "menu_trainingprovider" );
        $this->template->setAttribute("class", "active", "menu_trainingprovider", "ul/li/a[@href='instance_calendar']" );
    }

}
# Local Variables:
# mode: php
# c-default-style: "bsd"
# indent-tabs-mode: nil
# c-basic-offset: 4
# End:
