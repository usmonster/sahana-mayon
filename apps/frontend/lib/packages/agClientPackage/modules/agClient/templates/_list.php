<?php
//print_r($this->ag_persons);
/* The list itself should be a form
 * we also must learn the proper way to use doxygen and
 * it's documentation syntax, so these comments are picked up.
 * Also, of utmost current importance is to optimize the way
 * in which the ag_persons object comes into this form/list.
 * This should be a doctrine collection/array of objects/arrays
 * with each row representing the desired information for the
 * persons.  So, the list form constructor function (for filtering,
 * searching, sorting, usability, should expect an array of desired
 * attributes for persons. Even further refactorization would
 * dictate that such a list/form object should also ask what type
 * of result set main identifier to be working with... for example,
 * later, search results may be of different types.. example:
 * public class agPersonListForm extends agListForm
 *
 * (which extends sfFormObject which extends baseForm
 * BaseForm extends sfFormSymfony which extends sfForm
 * and then we get to the end of this....
 *
 * class sfForm implements ArrayAccess, Iterator, Countable
 *
 * and all of those implementations are in
 * Countable is in SPL.php
 * Iterator is in Core.php
 * ArrayAccess is in Core.php
 *
 * it would be assumed that there is a switch in the list layout
 * constructor that checks for a filter column.  i.e. is_filter,
 * would display a list of filterable items on the left... like new egg.
 * when constructing the form, all relations should be checked
 * for, and possibly embedded (as passed in by the filter array)
 */
?>

<form action="<?php echo url_for('staff/list'); ?>"">
      <table>
    <tfoot>
      <tr>
        <td>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $list ?>
      <?php
      /* echo $form['name']; /* this will echo the agEmbeddedNamesForm,
       * useful for laying out the page right
       */
      ?>
    </tbody>
  </table>
</form>