<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 * SilverSelenide extends the Selenium TestCase and includes
 * a magic method that allows every function with undefined
 * return value to return the implementing object's instance.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Usman Akeju, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class SilverSelenide extends PHPUnit_Extensions_SeleniumTestCase
{

  /**
   * Delegate method calls to the SeleniumTestCase.
   * Enables method chaining on most Selenese commands by <s>awesomely</s>
   * intelligently returning $this when no other return value is defined.
   *
   * @todo TEST THIS! make sure isset() check is correct & sufficient (try with boolean commands)
   * @see PHPUnit_Extensions_SeleniumTestCase::__call($command, $arguments)
   * @see http://netbeans.org/bugzilla/show_bug.cgi?id=150454
   *
   * @param  string $command
   * @param  array  $arguments
   * @return mixed
   * @method mixed  addLocationStrategy()
   * @method mixed  addLocationStrategyAndWait()
   * @method mixed  addScript()
   * @method mixed  addScriptAndWait()
   * @method mixed  addSelection()
   * @method mixed  addSelectionAndWait()
   * @method mixed  allowNativeXpath()
   * @method mixed  allowNativeXpathAndWait()
   * @method mixed  altKeyDown()
   * @method mixed  altKeyDownAndWait()
   * @method mixed  altKeyUp()
   * @method mixed  altKeyUpAndWait()
   * @method mixed  answerOnNextPrompt()
   * @method mixed  assignId()
   * @method mixed  assignIdAndWait()
   * @method mixed  attachFile()
   * @method mixed  break()
   * @method mixed  captureEntirePageScreenshot()
   * @method mixed  captureEntirePageScreenshotAndWait()
   * @method mixed  captureEntirePageScreenshotToStringAndWait()
   * @method mixed  captureScreenshotAndWait()
   * @method mixed  captureScreenshotToStringAndWait()
   * @method mixed  check()
   * @method mixed  checkAndWait()
   * @method mixed  chooseCancelOnNextConfirmation()
   * @method mixed  chooseCancelOnNextConfirmationAndWait()
   * @method mixed  chooseOkOnNextConfirmation()
   * @method mixed  chooseOkOnNextConfirmationAndWait()
   * @method mixed  click()
   * @method mixed  clickAndWait()
   * @method mixed  clickAt()
   * @method mixed  clickAtAndWait()
   * @method mixed  close()
   * @method mixed  contextMenu()
   * @method mixed  contextMenuAndWait()
   * @method mixed  contextMenuAt()
   * @method mixed  contextMenuAtAndWait()
   * @method mixed  controlKeyDown()
   * @method mixed  controlKeyDownAndWait()
   * @method mixed  controlKeyUp()
   * @method mixed  controlKeyUpAndWait()
   * @method mixed  createCookie()
   * @method mixed  createCookieAndWait()
   * @method mixed  deleteAllVisibleCookies()
   * @method mixed  deleteAllVisibleCookiesAndWait()
   * @method mixed  deleteCookie()
   * @method mixed  deleteCookieAndWait()
   * @method mixed  deselectPopUp()
   * @method mixed  deselectPopUpAndWait()
   * @method mixed  doubleClick()
   * @method mixed  doubleClickAndWait()
   * @method mixed  doubleClickAt()
   * @method mixed  doubleClickAtAndWait()
   * @method mixed  dragAndDrop()
   * @method mixed  dragAndDropAndWait()
   * @method mixed  dragAndDropToObject()
   * @method mixed  dragAndDropToObjectAndWait()
   * @method mixed  dragDrop()
   * @method mixed  dragDropAndWait()
   * @method mixed  echo()
   * @method mixed  fireEvent()
   * @method mixed  fireEventAndWait()
   * @method mixed  focus()
   * @method mixed  focusAndWait()
   * @method string   getAlert()
   * @method array    getAllButtons()
   * @method array    getAllFields()
   * @method array    getAllLinks()
   * @method array    getAllWindowIds()
   * @method array    getAllWindowNames()
   * @method array    getAllWindowTitles()
   * @method string   getAttribute()
   * @method array    getAttributeFromAllWindows()
   * @method string   getBodyText()
   * @method string   getConfirmation()
   * @method string   getCookie()
   * @method string   getCookieByName()
   * @method integer  getCursorPosition()
   * @method integer  getElementHeight()
   * @method integer  getElementIndex()
   * @method integer  getElementPositionLeft()
   * @method integer  getElementPositionTop()
   * @method integer  getElementWidth()
   * @method string   getEval()
   * @method string   getExpression()
   * @method string   getHtmlSource()
   * @method string   getLocation()
   * @method string   getLogMessages()
   * @method integer  getMouseSpeed()
   * @method string   getPrompt()
   * @method array    getSelectOptions()
   * @method string   getSelectedId()
   * @method array    getSelectedIds()
   * @method string   getSelectedIndex()
   * @method array    getSelectedIndexes()
   * @method string   getSelectedLabel()
   * @method array    getSelectedLabels()
   * @method string   getSelectedValue()
   * @method array    getSelectedValues()
   * @method mixed  getSpeed()
   * @method mixed  getSpeedAndWait()
   * @method string   getTable()
   * @method string   getText()
   * @method string   getTitle()
   * @method string   getValue()
   * @method boolean  getWhetherThisFrameMatchFrameExpression()
   * @method boolean  getWhetherThisWindowMatchWindowExpression()
   * @method integer  getXpathCount()
   * @method mixed  goBack()
   * @method mixed  goBackAndWait()
   * @method mixed  highlight()
   * @method mixed  highlightAndWait()
   * @method mixed  ignoreAttributesWithoutValue()
   * @method mixed  ignoreAttributesWithoutValueAndWait()
   * @method boolean  isAlertPresent()
   * @method boolean  isChecked()
   * @method boolean  isConfirmationPresent()
   * @method boolean  isCookiePresent()
   * @method boolean  isEditable()
   * @method boolean  isElementPresent()
   * @method boolean  isOrdered()
   * @method boolean  isPromptPresent()
   * @method boolean  isSomethingSelected()
   * @method boolean  isTextPresent()
   * @method boolean  isVisible()
   * @method mixed  keyDown()
   * @method mixed  keyDownAndWait()
   * @method mixed  keyDownNative()
   * @method mixed  keyDownNativeAndWait()
   * @method mixed  keyPress()
   * @method mixed  keyPressAndWait()
   * @method mixed  keyPressNative()
   * @method mixed  keyPressNativeAndWait()
   * @method mixed  keyUp()
   * @method mixed  keyUpAndWait()
   * @method mixed  keyUpNative()
   * @method mixed  keyUpNativeAndWait()
   * @method mixed  metaKeyDown()
   * @method mixed  metaKeyDownAndWait()
   * @method mixed  metaKeyUp()
   * @method mixed  metaKeyUpAndWait()
   * @method mixed  mouseDown()
   * @method mixed  mouseDownAndWait()
   * @method mixed  mouseDownAt()
   * @method mixed  mouseDownAtAndWait()
   * @method mixed  mouseMove()
   * @method mixed  mouseMoveAndWait()
   * @method mixed  mouseMoveAt()
   * @method mixed  mouseMoveAtAndWait()
   * @method mixed  mouseOut()
   * @method mixed  mouseOutAndWait()
   * @method mixed  mouseOver()
   * @method mixed  mouseOverAndWait()
   * @method mixed  mouseUp()
   * @method mixed  mouseUpAndWait()
   * @method mixed  mouseUpAt()
   * @method mixed  mouseUpAtAndWait()
   * @method mixed  mouseUpRight()
   * @method mixed  mouseUpRightAndWait()
   * @method mixed  mouseUpRightAt()
   * @method mixed  mouseUpRightAtAndWait()
   * @method mixed  open()
   * @method mixed  openWindow()
   * @method mixed  openWindowAndWait()
   * @method mixed  pause()
   * @method mixed  refresh()
   * @method mixed  refreshAndWait()
   * @method mixed  removeAllSelections()
   * @method mixed  removeAllSelectionsAndWait()
   * @method mixed  removeScript()
   * @method mixed  removeScriptAndWait()
   * @method mixed  removeSelection()
   * @method mixed  removeSelectionAndWait()
   * @method mixed  retrieveLastRemoteControlLogs()
   * @method mixed  rollup()
   * @method mixed  rollupAndWait()
   * @method mixed  runScript()
   * @method mixed  runScriptAndWait()
   * @method mixed  select()
   * @method mixed  selectAndWait()
   * @method mixed  selectFrame()
   * @method mixed  selectPopUp()
   * @method mixed  selectPopUpAndWait()
   * @method mixed  selectWindow()
   * @method mixed  setBrowserLogLevel()
   * @method mixed  setBrowserLogLevelAndWait()
   * @method mixed  setContext()
   * @method mixed  setCursorPosition()
   * @method mixed  setCursorPositionAndWait()
   * @method mixed  setMouseSpeed()
   * @method mixed  setMouseSpeedAndWait()
   * @method mixed  setSpeed()
   * @method mixed  setSpeedAndWait()
   * @method mixed  shiftKeyDown()
   * @method mixed  shiftKeyDownAndWait()
   * @method mixed  shiftKeyUp()
   * @method mixed  shiftKeyUpAndWait()
   * @method mixed  shutDownSeleniumServer()
   * @method mixed  store()
   * @method mixed  submit()
   * @method mixed  submitAndWait()
   * @method mixed  type()
   * @method mixed  typeAndWait()
   * @method mixed  typeKeys()
   * @method mixed  typeKeysAndWait()
   * @method mixed  uncheck()
   * @method mixed  uncheckAndWait()
   * @method mixed  useXpathLibrary()
   * @method mixed  useXpathLibraryAndWait()
   * @method mixed  waitForCondition()
   * @method mixed  waitForPageToLoad()
   * @method mixed  waitForPopUp()
   * @method mixed  windowFocus()
   * @method mixed  windowMaximize()
   */
  public function __call($command, $arguments)
  {
    $return = parent::__call($command, $arguments);
    if (isset($return)) {
      return $return;
    }
    return  $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementContainsText()
   */
  public function assertElementContainsText($locator, $text, $message = '')
  {
    parent::assertElementContainsText($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementNotContainsText()
   */
  public function assertElementNotContainsText($locator, $text, $message = '')
  {
    parent::assertElementNotContainsText($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementValueContains()
   */
  public function assertElementValueContains($locator, $text, $message = '')
  {
    parent::assertElementValueContains($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementValueNotContains()
   */
  public function assertElementValueNotContains($locator, $text, $message = '')
  {
    parent::assertElementValueNotContains($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementValueEquals()
   */
  public function assertElementValueEquals($locator, $text, $message = '')
  {
    parent::assertElementValueEquals($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertElementValueNotEquals()
   */
  public function assertElementValueNotEquals($locator, $text, $message = '')
  {
    parent::assertElementValueNotEquals($locator, $text, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertIsSelected()
   */
  public function assertIsSelected($selectLocator, $value, $message = '')
  {
    parent::assertIsSelected($selectLocator, $value, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertIsNotSelected()
   */
  public function assertIsNotSelected($selectLocator, $value, $message = '')
  {
    parent::assertIsNotSelected($selectLocator, $value, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertSelected()
   */
  public function assertSelected($selectLocator, $option, $message = '')
  {
    parent::assertSelected($selectLocator, $option, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertNotSelected()
   */
  public function assertNotSelected($selectLocator, $option, $message = '')
  {
    parent::assertNotSelected($selectLocator, $option, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertSelectHasOption()
   */
  public function assertSelectHasOption($selectLocator, $option, $message = '')
  {
    parent::assertSelectHasOption($selectLocator, $option, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::assertSelectNotHasOption()
   */
  public function assertSelectNotHasOption($selectLocator, $option, $message = '')
  {
    parent::assertSelectNotHasOption($selectLocator, $option, $message);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::runDefaultAssertions()
   */
  public function runDefaultAssertions($action)
  {
    parent::runDefaultAssertions($action);
    return $this;
  }

  /**
   * @return mixed an instance of $this test
   * @see PHPUnit_Extensions_SeleniumTestCase::runSelenese()
   */
  public function runSelenese($filename)
  {
    parent::runSelenese($filename);
    return $this;
  }

}
