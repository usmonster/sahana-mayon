[

{

  rows: [
          {
<?php foreach ($event_facility_resources as $key => $value): //$nb1 = count($job); $j = 0; ++$j ?>
    id: "<?php echo $key ?>"
    cells: {
      facility_name: "<?php echo $value['f_facility_name'] ?>"
      facility_code: "<?php echo $value['f_facility_code'] ?>"
      work_phone: "<?php echo '$work_phone' ?>"
      work_email: "<?php echo '$work_email' ?>"
      facility_resource_type_abbr: "<?php echo $value['frt_facility_resource_type_abbr'] ?>"
      facility_group: "<?php echo $value['sfg_scenario_facility_group'] //should be event facility group?>"
    }
  

<?php endforeach ?>
}

]
}
]