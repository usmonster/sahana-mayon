<?php
class agScenarioGenerator
{
  /**
   * @method shiftGenerator()
   * Auto-generate shifts from ag_shift_template table to ag_scenario_shift table.
   * @return shift template entries.
   */
  public static function shiftGenerator()
  {
    try {
      // Query for the information to populate scenario shift.
      $rawQuery = new Doctrine_RawSql();
      $rawQuery->select('{st.*}, {fsr.*}, {sfr.*}')
        ->from('ag_shift_template AS st')
        ->innerJoin('ag_facility_staff_resource AS fsr ON fsr.staff_resource_type_id = st.staff_resource_type_id')
        ->innerJoin('ag_scenario_facility_resource AS sfr ON sfr.id = fsr.scenario_facility_resource_id')
        ->innerJoin('ag_facility_resource AS fr1 ON fr1.id = sfr.facility_resource_id')
        ->innerJoin('ag_facility_resource AS fr2 ON fr2.facility_resource_type_id = st.facility_resource_type_id')
        ->innerJoin('ag_scenario_facility_group AS sfg ON sfg.scenario_id = st.scenario_id')
        ->where('fr1.id=fr2.id')
        ->orderBy('sfg.scenario_id, sfr.id, st.staff_resource_type_id')
        ->addComponent('st', 'agShiftTemplate st')
        ->addComponent('fsr', 'st.agFacilityStaffResource fsr')
        ->addComponent('sfr', 'fsr.agScenarioFacilityResource sfr');
      $resultSet = $rawQuery->execute(array(), Doctrine::HYDRATE_SCALAR);

      // Delete all scenario shift records prior to generating scenario shifts from shift template tables.
      $deleteQuery = Doctrine_Query::create()
        ->delete()
        ->from('agScenarioShift')
        ->execute();

      $id_counter = 1;
      foreach ($resultSet as $row)
      {
        $shift_repeat_counter = 0;
        $staff_wave = 1;
        $staff_wave_counter = 1;
        $reset_break_length = $row['st_break_length_minutes'];
        $reset_minutes_start_to_facility_activation = $row['st_minutes_start_to_facility_activation'];

        while ( $shift_repeat_counter <= $row['st_shift_repeats'] )
        {
          // max_staff_repeat_shifts should be double since staff can only work every other shifts.
          if ( $staff_wave_counter > ($row['st_max_staff_repeat_shifts'] * 2) )
          {
            $staff_wave++;
            $reset_break_length = 0;
            $staff_wave_counter = 1;
          } else {
            $staff_wave_counter++;
            $reset_break_length = $row['st_break_length_minutes'];
          }

          $reset_break_length = ( $shift_repeat_counter == $row['st_shift_repeats'] ) ? 0 : $row['st_break_length_minutes'];
          $reset_minutes_start_to_facility_activation = ($shift_repeat_counter == 0 ) ? $row['st_minutes_start_to_facility_activation'] : $reset_minutes_start_to_facility_activation + $row['st_task_length_minutes'];

          $scenarioShift = new agScenarioShift();
          $scenarioShift->set('scenario_facility_resource_id', $row['sfr_id']);
          $scenarioShift->set('staff_resource_type_id', $row['st_staff_resource_type_id']);
          $scenarioShift->set('task_id', $row['st_task_id']);
          $scenarioShift->set('task_length_minutes', $row['st_task_length_minutes']);
          $scenarioShift->set('break_length_minutes', $reset_break_length);
          $scenarioShift->set('minutes_start_to_facility_activation', $reset_minutes_start_to_facility_activation);
          $scenarioShift->set('minimum_staff', $row['fsr_minimum_staff']);
          $scenarioShift->set('maximum_staff', $row['fsr_maximum_staff']);
          $scenarioShift->set('staff_wave', $staff_wave);
          $scenarioShift->set('shift_status_id', $row['st_shift_status_id']);
          $scenarioShift->set('deployment_algorithm_id', $row['st_deployment_algorithm_id']);
          $scenarioShift->set('originator_id', $row['st_id']);
          $scenarioShift->save();
          $shift_repeat_counter++;
          $id_counter++;
        }
      }
    } catch (\Doctrine\ORM\ORMException $e) {
      print_r($e);
      return NULL;
    }
  }
}

?>
