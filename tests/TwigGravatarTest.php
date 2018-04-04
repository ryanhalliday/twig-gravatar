<?php
class TwigGravatarTest extends \PHPUnit_Framework_TestCase{
	/** @var TwigGravatar */
	protected $twigGravatar;

	protected $email;
	protected $hash;
	protected $url;
	protected $secureUrl;

	public function setUp(){
		$this->twigGravatar = new \TwigGravatar();

		$this->email = "example@example.com";
		$this->hash = "23463b99b62a72f26ed677cc556c44e8";
		$this->url = "http://www.gravatar.com/avatar/".$this->hash;
		$this->secureUrl = "https://secure.gravatar.com/avatar/".$this->hash;
	}

	public function tearDown(){}

	public function testGetFilters(){
		$filterNames = array();
		$filters = $this->twigGravatar->getFilters();

		/** @var Twig_SimpleFilter $twigSimpleFilter */
		foreach ($filters as $twigSimpleFilter) {
			$filterNames[] = $twigSimpleFilter->getName();
		}

		$this->assertContains('grAvatar', $filterNames);
		$this->assertContains('grHttps', $filterNames);
		$this->assertContains('grSize', $filterNames);
		$this->assertContains('grDefault', $filterNames);
		$this->assertContains('grRating', $filterNames);
	}

	public function testAvatar(){
		$avatar = $this->twigGravatar->avatar($this->email);
		$this->assertEquals($this->secureUrl, $avatar);
	}

	public function testInvalidAvatar(){
		$this->setExpectedException("InvalidArgumentException");
		$avatar = $this->twigGravatar->avatar("invalid@@email..com");
	}

	public function testHttps(){
		$secureUrl = $this->twigGravatar->https($this->url);
		$this->assertEquals($this->secureUrl, $secureUrl);
	}

	public function testInvalidUrl(){
		$invalidUrl = "http://not.gravatar.at.all";
		$functions = array(
			'https' => null,
			'size' => 20,
			'def' => null,
			'rating' => 'r'
		);
		$exceptionCount = 0;

		foreach ($functions as $function => $argument){
			try{
				$this->twigGravatar->$function($invalidUrl, $argument);
			} catch (Exception $e){
				$exceptionCount++;
				$this->assertContains("existing Gravatar URL", $e->getMessage());
			}
		}
		$this->assertEquals(sizeof($functions), $exceptionCount);
	}

	public function testSize(){
		$sized = $this->twigGravatar->size($this->url, 100);
		$this->assertEquals($this->url."?size=100", $sized);
	}

	public function testInvalidSize(){
		$sizes = array("abc", -1, 3000);
		$exceptionCount = 0;

		foreach ($sizes as $size){
			try{
				echo $this->twigGravatar->size($this->url, $size);
			} catch (InvalidArgumentException $e){
				$exceptionCount++;
				$this->assertEquals("You must pass the size filter a valid number between 0 and 2048", $e->getMessage());
			}
		}
		$this->assertEquals(sizeof($sizes), $exceptionCount);
	}

	public function testDefWithForcedValueTrue(){
		$defaulted = $this->twigGravatar->def($this->url, "blank", true);
		$this->assertEquals($this->url."?default=blank&forcedefault=y", $defaulted);
	}

	public function testDefWithForcedValueFalse(){
		$defaulted = $this->twigGravatar->def($this->url, "blank", false);
		$this->assertEquals($this->url."?default=blank", $defaulted);
	}

	public function testDefWithoutForcedValue(){
		$defaulted = $this->twigGravatar->def($this->url, "blank");
		$this->assertEquals($this->url."?default=blank", $defaulted);
	}

	public function testInvalidDef(){
		$exceptionCount = 0;

		try{
			$this->twigGravatar->def($this->url, "thisisnotanavatarorurl");
		} catch (InvalidArgumentException $e){
			$exceptionCount++;
			$this->assertEquals('Default must be a URL or valid default', $e->getMessage());
		}

		try{
			$this->twigGravatar->def($this->url, "blank", "probably");
		} catch (InvalidArgumentException $e){
			$exceptionCount++;
			$this->assertEquals("The force option for a default must be boolean", $e->getMessage());
		}

		$this->assertEquals(2, $exceptionCount);
	}

	public function testRating(){
		$rated = $this->twigGravatar->rating($this->url, "r");
		$this->assertEquals($this->url."?rating=r", $rated);
	}

	public function testInvalidRating(){
		$exceptionCount = 0;

		try{
			$this->twigGravatar->rating($this->url, "y");
		} catch (InvalidArgumentException $e){
			$exceptionCount++;
			$this->assertEquals("Rating must be g,pg,r or x", $e->getMessage());
		}

		$this->assertEquals(1, $exceptionCount);
	}

	public function testGenerateHash(){
		$hash = $this->twigGravatar->generateHash($this->email);
		$this->assertEquals($this->hash, $hash);
	}

	public function testGetName(){
		$name = $this->twigGravatar->getName();
		$this->assertEquals("TwigGravatar", $name);
	}
}
