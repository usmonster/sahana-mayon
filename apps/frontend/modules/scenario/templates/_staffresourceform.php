<?php use_javascript('jquery.ui.custom.js'); ?>
<?php //TODO: see if this is still necessary:
use_javascript('tooltip');?>
<?php use_javascript('json.serialize.js'); ?>
<?php use_javascript('inlineedit.js'); ?>


<?php
if (isset($formsArray)){
  foreach ($formsArray as $key => $f) {
    // Create array of form names.
    $groupNames[] = $key;
  }
}
?>

<script type="text/javascript">

    (function($) {

        $.fn.inlineEdit = function(options) {

            // define some options with sensible default values
            // - hoverClass: the css classname for the hover style
            options = $.extend({
                hoverClass: 'hover'
            }, options);

            return $.each(this, function() {

                // define self container
                var self = $(this);

                // create a value property to keep track of current value
                self.value = self.text();

                // bind the click event to the current element, in this example it's span.editable
                self.bind('click', function() {

                    self
                        // populate current element with an input element and add the current value to it
                        .html('<input type="text" value="'+ self.value +'">')
                        // select this newly created input element
                        .find('input')
                            // bind the blur event and make it save back the value to the original span area
                            // there by replacing our dynamically generated input element
                            .bind('blur', function(event) {
                                self.value = $(this).val();
                                self.text(self.value);
                            })
                            // give the newly created input element focus
                            .focus();

                })
                // on hover add hoverClass, on rollout remove hoverClass
                .hover(
                    function(){
                        self.addClass(options.hoverClass);
                    },
                    function(){
                        self.removeClass(options.hoverClass);
                    }
                );
            });
        }

    })(jQuery);

    $(function(){
        $('.editable').inlineEdit();
    });


$(function(){
    $('.inputFraySmall').click(function(){ //we need this to happen on page load, all elements of a class that HAVE text in them, set html attr to span
      $(this).html('<span class="inputGraySmall">' + $(this).valueOf()+'</span>');
      });//this  has been disabled to not break anything while it's still in pro'


    $('.groupLabel').click(function(){ //this needs to be only the children of ..currently it's ALL
      $(this).parent().find('.facgroup').slideToggle("slow");
    });
});

</script>
<!--
this is in here as a place holder for future development to
<input type="text"  class="editable">foobar</span> -->
<form action="<?php echo url_for('scenario/staffresources?id=' . $scenario->id) ?>" method="post">
  <?php
    //this is the same form that should be used for edit and create.
    //display entered values if the objects exist.
    //
    // since this is the partial, we have to refer to view layer items with $this
    //
    // Set up the container form and its formatter.
    //$a_record = new agFacilityStaffResource(); //get an existing record if it exists
    //echo editable_content_tag('span', $a_record,'minimum_staff');


    echo $facilityStaffResourceContainer;
  ?>
  <br />
  <br />
  <input class="linkButton" type="submit" value="Save" />
  <input class="linkButton" type="submit" value="Save and Continue" name="Continue"/>
</form>


<?php if(isset($scenarioFacilityGroups)){ ?>
<?php foreach ($scenarioFacilityGroups as $facilityGroup): ?>
<table class="singleTable">
  <thead>
      <caption><?php echo $facilityGroup->scenario_facility_group;?></caption>
  </thead>
  <tbody>
    <?php foreach ($facilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource): ?>
    <tr>
      <th class="head" colspan="<?php echo (count($scenarioFacilityResource->getAgFacilityStaffResource()) * 3);?>"><?php echo $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type); ?></th>
    </tr>
    <tr>
      <?php foreach ($scenarioFacilityResource->getAgFacilityStaffResource() as $key => $staffResourceType): ?>
      <th class="<?php echo (($key == 0) ? 'subHeadLeft' : 'subHeadMid'); ?>"><?php echo ucwords($staffResourceType->getAgStaffResourceType()->staff_resource_type); ?></th>
      <td>Min: <?php echo $staffResourceType->minimum_staff; ?></td>
      <td>Max: <?php echo $staffResourceType->maximum_staff; ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />
<?php endforeach; ?>
<?php
}
?>