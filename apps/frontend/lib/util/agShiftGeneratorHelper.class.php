<?php

/**
 * Shift generator for scenario
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */

class agShiftGeneratorHelper
{

  /**
   * Auto-generate shifts from ag_shift_template table to ag_scenario_shift table.
   * @param integer $scenarioId A valid scenario ID.
   * @return integer Returns 1 if successful.  Otherwise, 0.
   */
  public static function shiftGenerator($scenarioId, Doctrine_Connection $conn = NULL)
  {
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }


    try {
      // Query for the information to populate scenario shift.
      $scenarioShifts = agDoctrineQuery::create()
              ->select('st.*, fsr.*')
              ->from('agShiftTemplate st')
              ->innerJoin('st.agFacilityResourceType fst')
              ->innerJoin('fst.agFacilityResource fr')
              ->innerJoin('fr.agScenarioFacilityResource sfr1')
              ->innerJoin('sfr1.agScenarioFacilityGroup sfg')
              ->innerJoin('st.agStaffResourceType srt')
              ->innerJoin('srt.agFacilityStaffResource fsr')
              ->innerJoin('fsr.agScenarioFacilityResource sfr2')
              ->where('sfr1.id = sfr2.id')
              ->andWhere('st.scenario_id = sfg.scenario_id')
              ->andWhere('st.scenario_id = ?', $scenarioId)
              ->execute(array(), Doctrine::HYDRATE_SCALAR);

      // Delete all scenario shift records prior to generating scenario shifts
      // from shift template tables.

      $delExists = 'scenario_facility_resource_id IN (' .
        'SELECT ssfr.id ' .
          'FROM agScenarioFacilityResource AS ssfr ' .
            'INNER JOIN ssfr.agScenarioFacilityGroup AS ssfg ' .
          'WHERE ssfg.scenario_id = ?)';
      $deleteQuery = agDoctrineQuery::create($conn)
              ->delete()
              ->from('agScenarioShift')
              ->where($delExists, $scenarioId)
              ->execute();

      foreach ($scenarioShifts as $row) {
        $shift_counter = 1;
        $staff_wave = 1;
        $reset_break_length = $row['st_break_length_minutes'];
        $reset_minutes_start_to_facility_activation = $row['st_minutes_start_to_facility_activation'];

        // calculate the true number of repeats
        if ($row['st_task_length_minutes'] > 0)
        {
          $shiftRepeats = ceil(($row['st_days_in_operation'] * ((24*60) / $row['st_task_length_minutes'])))-1;
        }
        else
        {
          $shiftRepeats = 0;
        }

        while ($shift_counter <= $shiftRepeats) {
          // A staff should only be working at one shift and rest while the
          // next following shift starts.  He/she should only be assigned to
          // every other shifts if a staff should work multiple shifts.  Thus,
          // the staff wave is multipled by two.
          $staff_shift_repeat = $row['st_max_staff_repeat_shifts'] * 2;

          if (($shift_counter != 1) && (($shift_counter % $staff_shift_repeat) == 1)) {
            $staff_wave += 2;
          }

          // Release staffs as they finish their last shift.
          if ((($shift_counter % $staff_shift_repeat) == 0) ||
              (($shift_counter % $staff_shift_repeat) == ($staff_shift_repeat - 1)) ||
              ($shift_counter == ($row['st_days_in_operation'] + 1)) ||
              ($shift_counter == $row['st_days_in_operation'])) {
            $reset_break_length = 0;
          } else {
            $reset_break_length = $row['st_break_length_minutes'];
          }

          
          if ($shift_counter == 1 )
          {
            $reset_minutes_start_to_facility_activation =  $row['st_minutes_start_to_facility_activation'];
          }
          else
          {
            $reset_minutes_start_to_facility_activation = $reset_minutes_start_to_facility_activation + $row['st_task_length_minutes'];
          }

          $scenarioShift = new agScenarioShift();
          $scenarioShift->set('scenario_facility_resource_id', $row['fsr_scenario_facility_resource_id'])
              ->set('staff_resource_type_id', $row['st_staff_resource_type_id'])
              ->set('task_id', $row['st_task_id'])
              ->set('task_length_minutes', $row['st_task_length_minutes'])
              ->set('break_length_minutes', $reset_break_length)
              ->set('minutes_start_to_facility_activation', $reset_minutes_start_to_facility_activation)
              ->set('minimum_staff', $row['fsr_minimum_staff'])
              ->set('maximum_staff', $row['fsr_maximum_staff'])
              ->set('staff_wave', $shift_counter & 1 ? $staff_wave : $staff_wave + 1)
              ->set('shift_status_id', $row['st_shift_status_id'])
              ->set('deployment_algorithm_id', $row['st_deployment_algorithm_id'])
              ->set('originator_id', $row['st_id']);
          $scenarioShift->save($conn);
          $shift_counter++;
        }
      }
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
      return 1;
    } catch (Exception $e) {
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      $eMsg = 'The application encountered an error during automatic generation of shifts. No ' .
        'shifts were created at this time.';
      sfContext::getInstance()->getLogger()->err($eMsg);
      sfContext::getInstance()->getLogger()->err($e->getMessage());

      echo $eMsg;
      return 0;
    }
  }
}
