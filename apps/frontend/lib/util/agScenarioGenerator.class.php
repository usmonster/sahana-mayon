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

class agScenarioGenerator
{

  /**
   * @method shiftGenerator()
   * Auto-generate shifts from ag_shift_template table to ag_scenario_shift table.
   * @return integer Returns 1 if successful.  Otherwise, 0.
   */
  public static function shiftGenerator()
  {
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
              ->execute(array(), Doctrine::HYDRATE_SCALAR);

      // Delete all scenario shift records prior to generating scenario shifts
      // from shift template tables.
      $deleteQuery = agDoctrineQuery::create()
              ->delete()
              ->from('agScenarioShift')
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

          $reset_minutes_start_to_facility_activation = ($shift_counter == 1 ) ? $row['st_minutes_start_to_facility_activation'] : $reset_minutes_start_to_facility_activation + $row['st_task_length_minutes'];

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
          $scenarioShift->save();
          $shift_counter++;
        }
      }
      return 1;
    } catch (\Doctrine\ORM\ORMException $e) {
      print_r($e);
      return 0;
    }
  }

  /**
   * @method staffPoolGenerator()
   * Auto-generate staff pool using lucene search defined in agScenarioStaffGenerator to agScenarioStaffResource table.
   * @return array
   */
  public static function staffPoolGenerator($lucene_query, $scenario_id)
  {
    try {
      $staff_resources = array();
      $staff_id = array();
      $scenarioAction = new agActions(sfContext::getInstance(), 'scenario', 'staffpool');  //this may be excessive as we are creating an entirely new object
      $scenarioAction->doSearch($lucene_query, FALSE);
      foreach ($scenarioAction->hits as $hit) {
        $staff_id[] = $scenarioAction->results[$hit->model][$hit->pk]['id'];
      }
      if (count($staff_id) > 0) {
        $staff_resource_dql = agDoctrineQuery::create()

          ->select('a.id')
            ->from('agStaffResource a')
              ->leftJoin('a.agStaffResourceStatus asrs')
              ->leftJoin('a.agScenarioStaffResource asr WITH asr.scenario_id = ?', $scenario_id)
            ->whereIn('a.staff_id', $staff_id)
              ->andWhere('asr.staff_resource_id IS NULL')
              ->andWhere('asrs.is_available = ?', true);
                
        $staff_resource_sql =  $staff_resource_dql->getSqlQuery();
        $staff_resources  = $staff_resource_dql->execute(array(), 'single_value_array');

      }

      return $staff_resources;
    } catch (\Doctrine\ORM\ORMException $e) {
      print_r($e);
      return 0;
    }
  }

  /**
   * @method saveStaffPool()
   * Save staff resource pool to db.
   * @param array $staff_resources A single value array of staff ids.
   * @return 1|0 Returns 1 if successful.  Otherwise, 0.
   */
  public static function saveStaffPool($staff_resources, $scenario_id, $search_weight)
  {
    try {
      foreach ($staff_resources as $staff_resource) {
        // TODO check to see if staff_resources exist(update), or we are making new
        //this is a first pass, we should do a bulk check to update/insert as needed
        $scenario_staff_resource = agDoctrineQuery::create()
                ->select('asr.id')
                ->from('agScenarioStaffResource asr')
                ->where('asr.staff_resource_id = ?', $staff_resource)
                ->execute();
        if (count($scenario_staff_resource) < 1) {
          $scenario_staff_resource = new agScenarioStaffResource();
          $scenario_staff_resource->set('staff_resource_id', $staff_resource)
              ->set('scenario_id', $scenario_id)
              ->set('deployment_weight', $search_weight);
        }
        $scenario_staff_resource->save();
      }
      return 1;
    } catch (\Doctrine\ORM\ORMException $e) {
      print_r($e);
      return 0;
    }
  }

}

?>
