<?php
/**
 * @category    Oink
 * @package     Oink_Oink
 */
class Oink_Oink_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /*
     * Oink helper
     * @var Oink_Oink_Helper_Data
     */
    protected $_helper;
    
    public function setUp()
    {
        $this->_helper=Mage::helper("oink");
        parent::setUp();
    }

    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function goodLogin($user,$password)
    {
        $helper=$this->_helper;
        $result = $helper->authenticateChild($user, $password);
        $this->assertTrue((bool)$result,"The user and password are correct, but the login fails.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function badLogin($user,$password)
    {
        $helper=$this->_helper;
        $result = $helper->authenticateChild($user, $password);
        $this->assertFalse($result,"The user and password are incorrect, but the login success.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function badLoginMultipleTimes($user,$password)
    {
        $helper=$this->_helper;
        for($i=0;$i<11;$i++){
            $result = $helper->authenticateUser($user, $password);
        }
        $this->assertFalse($result,"The user and password are incorrect, but the login success.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @dataProvider dataProvider
     */
    public function getUserAddress($user,$password)
    {
        $helper=$this->_helper;
        $helper->authenticateChild($user, $password);
        $children=Mage::helper("oink")->getUser();
        $address=$children->getAddress();
        $this->assertEquals("",(string)$address->ErrorMessage,"The user and password are correct, but the address have an error.");
    }
    
    /**
     *
     * @param string $user
     * @param string $password
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function processTransaction($user,$password){
		/*
		 * Test not working
		 * Test not obtaining same result as in normal magento process
		 * Dispatch coupled to magento's framework
		 * Will fix this when last phase of project is complete
		 * */
        $helper=$this->_helper;
        $helper->authenticateChild($user, $password);
        $quote=$helper->getQuote();
        $product1=Mage::getModel("catalog/product")->load(1);
        $product2=Mage::getModel("catalog/product")->load(2);
        $quote->addProduct($product1);
        $quote->addProduct($product2);
        Mage::helper("oink/checkout")->populateQuote();
        $cart=Mage::helper("oink/checkout")->getOinkCart();
        $result=Mage::helper("oink/checkout")->sendCartToOink($cart);
        var_dump($result);
//      $controllerTestCase=new EcomDev_PHPUnit_Test_Case_Controller();
//      $controllerTestCase->dispatch("oink/checkout/index");
    }
    
    
}