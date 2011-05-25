[

{

  rows: [
          {
<?php foreach ($job as $key => $value): //$nb1 = count($job); $j = 0; ++$j ?>
    id: "<?php echo $facility_resource_id ?>"
    cells: {
      facility_name: "<?php echo $facility_name ?>"
      facility_code: "<?php echo $facility_code ?>"
      work_phone: "<?php echo $work_phone ?>"
      work_email: "<?php echo $work_email ?>"
      facility_resource_type_abbr: "<?php echo $facility_resource_type_abbr ?>"
      facility_group: "<?php echo $facility_group ?>"
  }
  

<?php endforeach ?>
}

]
}
]