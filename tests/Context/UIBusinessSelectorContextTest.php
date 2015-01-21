<?php

use Behat\Mink\Mink;
use OrangeDigital\BusinessSelectorExtension\Context\UIBusinessSelectorContext;
use Behat\Mink\Session;
use Behat\Mink\Element\Element;
use \Mockery as m;

/**
 * Unit tests for the UIBusinessSelectorContext.
 * 
 * @author Ben Waine <ben.waine@orange.com>
 * @author Phill Hicks <phillip.hicks@orange.com>    
 */
class UIBusinessSelectorContextTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var OrangeDigital\OrangeExtension\Context\UIBusinessSelectorContext  
     */
    protected $context;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mink;
     
    protected $session;

    public function setUp() {

        $this->context = new UiBusinessSelectorContext(array(
                    "urlFilePath" => "tests/testfiles/urls.yml",
                    "selectorFilePath" => "tests/testfiles/selectors.yml",
                    "assetPath" => "tests/testfiles/assets/"

                ));

        $this->mink = $this->getMock('\Behat\Mink\Mink', array(), array(), '', false, false);


        $this->context->setMink($this->mink);
    }

    protected function setSessionExpectation($willCall = true) {
        $this->session = $this->getMock('Behat\Mink\Session', array(), array(), '', false, false);

        if ($willCall) {

            $this->mink
                    ->expects($this->any())
                    ->method('getSession')
                    ->will($this->returnValue($this->session));
        } else {
            $this->mink
                    ->expects($this->never())
                    ->method('getSession');
        }


    }

    protected function setFindExpectationWithReturnElement($selector, $element) {
        $page = $this->getMock('Behat\Mink\Element\Element', array(), array(), '', false, false);

        $page
                ->expects($this->once())
                ->method('find')
                ->with('css', $selector)
                ->will($this->returnValue($element));

        $this->session
                ->expects($this->any())
                ->method("getPage")
                ->will($this->returnValue($page));


    }

    protected function setFindExpectationWithNoReturnElement($selector) {
        $page = $this->getMock('Behat\Mink\Element\Element', array(), array(), '', false, false);

        $page
                ->expects($this->once())
                ->method('find')
                ->with('css', $selector)
                ->will($this->returnValue(null));

        $this->session
                ->expects($this->once())
                ->method("getPage")
                ->will($this->returnValue($page));
    }

    protected function setFindExpectationWithNoElementFoundException($selector) {
        $page = $this->getMock('Behat\Mink\Element\Element', array(), array(), '', false, false);

        $page
                ->expects($this->once())
                ->method('find')
                ->with('css', $selector)
                ->will($this->returnValue(null));

        $this->session
                ->expects($this->once())
                ->method("getPage")
                ->will($this->returnValue($page));

        $this->setExpectedException('OrangeDigital\BusinessSelectorExtension\Exception\ElementNotFoundException');
    }

////////////////////////////////////////////////////////////////////////////////


    public function testIGoToThePageShouldCorrectlySubstitutesPageName() {

        $this->setSessionExpectation();

        $this->session
                ->expects($this->once())
                ->method('visit')
                ->with("/user")
                ->will($this->returnValue(null));

        $this->context->iGoToThePage('User Home Page');
    }


    public function testIFollowTheLinkShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $link = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $link
                ->expects($this->once())
                ->method('click')
                ->will($this->returnValue(null));

        $this->setFindExpectationWithReturnElement('a.test', $link);

        $this->context->iFollowTheLink('User Link');
    }

    public function testIFollowTheLinkShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('a.test');

        $this->context->iFollowTheLink('User Link');
    }

    public function testSelectorFromStringNotFound()
    {
        $string = 'Test Token';
        $string = $this->context->getSelectorFromString($string, false);
        $this->assertEquals('Test Token', $string, "Test Token was not found but it was not returned as expected");
    }


    public function testSelectorFromStringNotFoundWithThrow()
    {
        $string_original = 'Test Token';
        $string = $this->context->getSelectorFromString($string_original, true);
        $this->assertEquals($string, $string_original);
    }


    public function testSelectorFromStringNotFoundWithThrowUsingDefault()
    {
        $string_original = 'Test Token';
        $string = $this->context->getSelectorFromString($string_original);
        $this->assertEquals($string, $string_original);
    }


    public function testIFillInTheFieldWithShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $input
                ->expects($this->once())
                ->method('setValue')
                ->with('test value')
                ->will($this->returnValue(null));

        $this->setFindExpectationWithReturnElement('input[name=first_name]', $input);

        $this->context->iFillInTheFieldWith('User Name', 'test value');
    }

    public function testIFillInTheFieldWithShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('input[name=first_name]');

        $this->context->iFillInTheFieldWith('User Name', 'test value');
    }


    public function testISelectFromTheSelectorShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $selector = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $selector
                ->expects($this->once())
                ->method('selectOption')
                ->with('Female')
                ->will($this->returnValue(null));

        $this->setFindExpectationWithReturnElement('select', $selector);

        $this->context->iSelectFromTheSelector('Female', 'User Gender');
    }

    public function testISelectFromTheSelectorShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('select');

        $this->context->iSelectFromTheSelector('Female', 'User Gender');
    }

    public function tessIAdditionallySelectFromTheSelectorShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $selector = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $selector
                ->expects($this->once())
                ->method('selectOption')
                ->with('volvo', true)
                ->will($this->returnValue(null));

        $this->setFindExpectationWithReturnElement('select', $selector);

        $this->context->iAdditionallySelectFromTheSelector('Volvo', 'User Car');
    }

    public function tessIAdditionallySelectFromTheSelectorShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('select');

        $this->context->iAdditionallySelectFromTheSelector('Volvo', 'User Car');
    }



    /**
     * Checkboxes
     */

    /**
     * @test
     */
    public function the_step_ICheckTheCheckbox_should_correctly_substitute_selector() {

        $uibiz = $this->setUibiz();

        $uibiz->iCheckTheCheckbox('Terms Box');
    }

    /**
     * Setup the class for use to test using mockery
     * @return UIBusinessSelectorContext
     */
    protected function setUiBiz($return_false_from_mink = false)
    {
        //Arrange
        $return_value = ($return_false_from_mink == true) ? false : true;
        $page       = m::mock('\Behat\Mink\Element\DocumentElement');
        $page->shouldReceive('checkField')->andReturn($return_value);
        $page->shouldReceive('uncheckField')->andReturn($return_value);
        $page->shouldReceive('hasCheckedField')->andReturn($return_value);

        $session    = m::mock('\Behat\Mink\Session');
        $session->shouldReceive('stop')->andReturn(true);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('getPage')->andReturn($page);

        $mink       = m::mock('\Behat\Mink\Mink');
        $mink->shouldReceive('stopSessions')->andReturn(true);

        $mink->shouldReceive('getSession')->andReturn($session);

        $uibiz = new UiBusinessSelectorContext(array(
            "urlFilePath" => "tests/testfiles/urls.yml",
            "selectorFilePath" => "tests/testfiles/selectors.yml",
            "assetPath" => "tests/testfiles/assets/"
        ));

        //Act
        $uibiz->setMink($mink);
        return $uibiz;
    }



    public function testIUnCheckTheCheckboxShouldCorrectlySubstituteSelector() {

        $uibiz = $this->setUibiz();

        $uibiz->iUnCheckTheCheckbox('Terms Box');
    }

    public function testTheShouldBeCheckedShouldCorrectlySubstituteSelector() {

        $uibiz = $this->setUibiz();

        $uibiz->theShouldBeChecked('Terms Box');
    }


    public function testTheShouldNotBeCheckedShouldCorrectlySubstituteSelector() {

        $uibiz = $this->setUibiz($return_false_from_mink = true);

        $uibiz->theShouldNotBeChecked('Terms Box');
    }

    /**
     * End Checkboxes
     */




    public function testTheFormFieldShouldContainShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('input[name=first_name]');

        $this->context->theFormFieldShouldContain('User Name', "Test Value");
    }

    public function testTheFormFieldShouldNotContainShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $field = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $field
                ->expects($this->once())
                ->method('getValue')
                ->will($this->returnValue("Test"));

        $this->setFindExpectationWithReturnElement('input[name=first_name]', $field);

        $this->context->theFormFieldShouldNotContain('User Name', 'Test Value');
    }

    public function testTheFormFieldShouldNotContainShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('input[name=first_name]');

        $this->context->theFormFieldShouldNotContain('User Name', "Test Value");
    }

    public function testTheShouldContainShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $cbox
                ->expects($this->once())
                ->method('getText')
                ->will($this->returnValue("foo"));

        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->context->theShouldContain('Container', "Test Value");
    }

    public function testTheShouldContainShouldThrowExceptionIfDoesNotContain() {

        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $cbox
                ->expects($this->once())
                ->method('getText')
                ->will($this->returnValue("Test"));

        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->setExpectedException('\RuntimeException');

        $this->context->theShouldContain('Container', "Test Value");
    }

    public function testTheShouldContainShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('div.main');

        $this->context->theShouldContain('Container', "text");
    }


    public function testTheShouldNotContainShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $cbox
                ->expects($this->once())
                ->method('getText')
                ->will($this->returnValue("Test"));

        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->context->theShouldNotContain('Container', "Test Value");
    }

    public function testTheShouldNotContainShouldThrowExceptionIfElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('div.main');

        $this->context->theShouldNotContain('Container', "text");
    }
    
    public function testIShouldSeeComponentShouldCorrectlySubstituteSelector() {

        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->context->iShouldSeeComponent('Container');        
    }

    public function testIShouldSeeComponentShouldThrowExceptionIfElementNotFound() {
        
        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('div.main');

        $this->context->iShouldSeeComponent('Container');        
    }

    public function testIShouldNotSeeComponentShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation(true);

        $page = $this->getMock('Behat\Mink\Element\Element', array(), array(), '', false, false);

        $page
                ->expects($this->once())
                ->method('find')
                ->with('css', 'div.main')
                ->will($this->returnValue(null));

        $this->session
                ->expects($this->once())
                ->method("getPage")
                ->will($this->returnValue($page));

        $this->context->iShouldNotSeeComponent('Container');         
    }


    public function testIShouldNotSeeComponentShouldNotThrowExceptionIfComponentFoundAndNotVisible() {
        
        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);
        
        $cbox->expects($this->once())
             ->method('isVisible')
             ->will($this->returnValue(false));
        
        $this->setFindExpectationWithReturnElement('div.main', $cbox);
        
        try {
        $this->context->iShouldNotSeeComponent('Container');         
        } catch (\RuntimeException $e) {
            $this->fail("Runtime exception found when expecting no exception.");
        }
    }

    public function testShouldContainShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation(true);

        $cboxSub = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);
        
        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);
        
        $cbox->expects($this->once())
              ->method('find')
              ->with('css', 'div.sub')
              ->will($this->returnValue($cboxSub));
        
        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->context->shouldContain('Container', 'SubContainer');   
        
    }

    public function testShouldContainShouldThrowExceptionIfOutterDoesNotContainInner() {
        
        $this->setSessionExpectation(true);

        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);
        
        $cbox->expects($this->once())
              ->method('find')
              ->with('css', 'div.sub')
              ->will($this->returnValue(null));
        
        $this->setFindExpectationWithReturnElement('div.main', $cbox);

        $this->setExpectedException('OrangeDigital\BusinessSelectorExtension\Exception\ElementNotFoundException');
        
        $this->context->shouldContain('Container', 'SubContainer');         
    }

    public function testShouldContainShouldThrowExceptionIfOutterElementNotFound() {

        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('div.main');

        $this->context->shouldContain('Container', 'SubContainer');              
    }

    public function testShouldNotContainShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation(true);
        
        $cbox = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);
        
        $cbox->expects($this->once())
              ->method('find')
              ->with('css', 'div.sub')
              ->will($this->returnValue(null));
        
        $this->setFindExpectationWithReturnElement('div.main', $cbox);
        
        $this->context->shouldNotContain('Container', 'SubContainer');           
    }


    public function testShouldNotContainShouldThrowExceptionIfOuterElementNotFound() {
        
        $this->setSessionExpectation(true);

        $this->setFindExpectationWithNoElementFoundException('div.main');

        $this->context->shouldNotContain('Container', 'SubContainer');           
    }


    
    public function testIAttachToShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $input
                ->expects($this->once())
                ->method('attachFile')
                ->will($this->returnValue(true));

        $this->setFindExpectationWithReturnElement('input[name=picture]', $input);

        $this->context->iAttachTo('cat.jpeg', 'User Picture');       
    }
    

    public function testIHoverOverShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $input
                ->expects($this->once())
                ->method('mouseOver')
                ->will($this->returnValue(true));

        $this->setFindExpectationWithReturnElement('input[name=picture]', $input);

        $this->context->iHoverOver('User Picture');               
    }


    public function testIFocusOnTheIframeShouldCorrectlySubstituteSelector() {
        
        $this->setSessionExpectation();
        
        $this->session
                ->expects($this->once())
                ->method("switchToIFrame")
                ->with("frameid")
                ->will($this->returnValue(null));


        $this->context->IFocusOnTheIframe('Frame');   
        
    }
    
    public function testIFocusOnTheIFrameShouldThrowExceptionOnNonExistentSelector() {
        
        $this->setSessionExpectation(true);
        

        $this->context->IFocusOnTheIframe('NOFrame');   
    }
    
    
    public function testIRefocusOnThePrimaryPage() {
        
        $this->setSessionExpectation();
        
        $this->session
                ->expects($this->once())
                ->method("switchToIFrame")
                ->with($this->isNull())
                ->will($this->returnValue(null));

        $this->context->iRefocusOnThePrimaryPage('Frame');           
    }
    
    public function testWaitForComponentShouldCorrectlySubstituteSelector() {
        
        // Expected to appear
        // Does appear && is visible 
        
        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $input->expects($this->once())
                ->method('isVisible')
                ->will($this->returnValue(true));
        
        $this->setFindExpectationWithReturnElement('input[name=picture]', $input);
        
        $this->context->waitForComponent('User Picture');     
    }
    
    public function testWaitForComponentShouldNotThrowExceptionIfElementDisappearsWhenExpectedOffPage()
    { 
        // Expected to disappear
        
        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $this->setFindExpectationWithNoReturnElement('input[name=picture]');
        
        $this->context->waitForComponent('User Picture', 'dis'); 
    }
    
    public function testWaitForComponentShouldNotThrowExceptionIfElementDisappearsWhenExpectedCecomesInvisible() 
    { 
        // Expected to disappear
        // present on page but invisible
        
        $this->setSessionExpectation(true);

        $input = $this->getMock('Behat\Mink\Element\NodeElement', array(), array(), '', false, false);

        $input->expects($this->once())
                ->method('isVisible')
                ->will($this->returnValue(false));      
        
        $this->setFindExpectationWithReturnElement('input[name=picture]', $input);
        
        $this->context->waitForComponent('User Picture', 'dis'); 
    }
    
//    public function testWaitForComponentShouldThrowExceptionOnNonExistentSelector() {
//        $this->setSessionExpectation(true);
//
//        $this->context->waitForComponent('Stuffed Dog');
//    }


    protected function tearDown()
    {
        m::close();
    }



}

