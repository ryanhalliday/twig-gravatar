<?php
class TwigGravatarTest extends \PHPUnit_Framework_TestCase{
	protected $TwigGravatar;

	protected $Email;
	protected $Hash;
	protected $Url;
	protected $SecureUrl;

	public function setUp(){
		$this->TwigGravatar = new \TwigGravatar();

		$this->Email = "example@example.com";
		$this->Hash = "23463b99b62a72f26ed677cc556c44e8";
		$this->Url = "http://www.gravatar.com/avatar/".$this->Hash;
		$this->SecureUrl = "https://secure.gravatar.com/avatar/".$this->Hash;
	}

	public function tearDown(){}

	public function testGetFilters(){
		$filterNames = array();
		$filters = $this->TwigGravatar->getFilters();
		
		foreach ($filters as $twigSimpleFilter) {
			$filterNames[] = $twigSimpleFilter->getName();
		}

 
		$this->assertContains('grAvatar',$filterNames);
		$this->assertContains('grHttps',$filterNames);
		$this->assertContains('grSize',$filterNames);
		$this->assertContains('grDefault',$filterNames);
		$this->assertContains('grRating',$filterNames);
	}

	public function testAvatar(){
		$Avatar = $this->TwigGravatar->avatar($this->Email);
		$this->assertEquals($this->Url, $Avatar);
	}

	public function testInvalidAvatar(){
		$this->setExpectedException("InvalidArgumentException");
		$Avatar = $this->TwigGravatar->avatar("invalid@@email..com");
	}

	public function testHttps(){
		$SecureUrl = $this->TwigGravatar->https($this->Url);
		$this->assertEquals($this->SecureUrl, $SecureUrl);
	}

	public function testInvalidUrl(){
		$InvalidUrl = "http://not.gravatar.at.all";
		$Functions = array('https','size','def','rating');
		$ExceptionCount = 0;

		foreach ($Functions as $function){
			try{
				$this->TwigGravatar->$function($InvalidUrl);
			} catch (Exception $e){
				$ExceptionCount++;
				$this->assertContains("existing Gravatar URL", $e->getMessage());
			}
		}
		$this->assertEquals(sizeof($Functions), $ExceptionCount);
	}

	public function testSize(){
		$Sized = $this->TwigGravatar->size($this->Url, 100);
		$this->assertEquals($this->Url."?size=100", $Sized);
	}

	public function testInvalidSize(){
		$Sizes = array("abc", -1, 3000);
		$ExceptionCount = 0;

		foreach ($Sizes as $size){
			try{
				echo $this->TwigGravatar->size($this->Url, $size);
			} catch (InvalidArgumentException $e){
				$ExceptionCount++;
				$this->assertEquals("You must pass the size filter a valid number between 0 and 2048", $e->getMessage());
			}
		}
		$this->assertEquals(sizeof($Sizes), $ExceptionCount);
	}

	public function testDefWithForcedValueTrue(){
		$Defaulted = $this->TwigGravatar->def($this->Url, "blank", true);
		$this->assertEquals($this->Url."?default=blank&forcedefault=y", $Defaulted);
	}

	public function testDefWithForcedValueFalse(){
		$Defaulted = $this->TwigGravatar->def($this->Url, "blank", false);
		$this->assertEquals($this->Url."?default=blank", $Defaulted);
	}

	public function testDefWithoutForcedValue(){
		$Defaulted = $this->TwigGravatar->def($this->Url, "blank");
		$this->assertEquals($this->Url."?default=blank", $Defaulted);
	}

	public function testInvalidDef(){
		$ExceptionCount = 0;

		try{
			$this->TwigGravatar->def($this->Url, "thisisnotanavatarorurl");
		} catch (InvalidArgumentException $e){
			$ExceptionCount++;
			$this->assertEquals('Default must be a URL or valid default', $e->getMessage());
		}

		try{
			$this->TwigGravatar->def($this->Url, "blank", "probably");
		} catch (InvalidArgumentException $e){
			$ExceptionCount++;
			$this->assertEquals("The force option for a default must be boolean", $e->getMessage());
		}

		$this->assertEquals(2, $ExceptionCount);
	}

	public function testRating(){
		$Rated = $this->TwigGravatar->rating($this->Url, "r");
		$this->assertEquals($this->Url."?rating=r", $Rated);
	}

	public function testInvalidRating(){
		$ExceptionCount = 0;

		try{
			$this->TwigGravatar->rating($this->Url, "y");
		} catch (InvalidArgumentException $e){
			$ExceptionCount++;
			$this->assertEquals("Rating must be g,pg,r or x", $e->getMessage());
		}

		$this->assertEquals(1, $ExceptionCount);
	}

	public function testGenerateHash(){
		$Hash = $this->TwigGravatar->generateHash($this->Email);
		$this->assertEquals($this->Hash, $Hash);
	}

	public function testGetName(){
		$Name = $this->TwigGravatar->getName();
		$this->assertEquals("TwigGravatar", $Name);
	}
}
