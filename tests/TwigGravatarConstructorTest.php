<?php
class TwigGravatarConstructorTest extends \PHPUnit_Framework_TestCase{

	protected $email;
	protected $hash;
	protected $url;
	protected $secureUrl;

	public function setUp(){
		$this->email = "example@example.com";
		$this->hash = "23463b99b62a72f26ed677cc556c44e8";
		$this->url = "http://www.gravatar.com/avatar/".$this->hash;
		$this->secureUrl = "https://secure.gravatar.com/avatar/".$this->hash;
	}

	public function testDefault(){
		$twigGravatar = new \TwigGravatar('retro');

		$defaulted = $twigGravatar->avatar($this->email);
		$this->assertEquals($this->secureUrl."?default=retro", $defaulted);

		$defaulted = $twigGravatar->def($this->url, "mm");
		$this->assertEquals($this->url."?default=mm", $defaulted);
	}

	public function testSize(){
		$twigGravatar = new \TwigGravatar(null, 50);

		$sized = $twigGravatar->avatar($this->email);
		$this->assertEquals($this->secureUrl."?size=50", $sized);

		$sized = $twigGravatar->size($this->url, 100);
		$this->assertEquals($this->url."?size=100", $sized);
	}

	public function testFilterPrefixIsSetToSm(){
		$twigGravatar = new \TwigGravatar(null, null, 'sm');

		$filterNames = array();
		$filters = $twigGravatar->getFilters();

		/** @var Twig_SimpleFilter $twigSimpleFilter */
		foreach ($filters as $twigSimpleFilter) {
			$filterNames[] = $twigSimpleFilter->getName();
		}

		$this->assertContains('smAvatar',$filterNames);
		$this->assertContains('smHttps',$filterNames);
		$this->assertContains('smSize',$filterNames);
		$this->assertContains('smDefault',$filterNames);
		$this->assertContains('smRating',$filterNames);

		$this->assertNotContains('grAvatar',$filterNames);
		$this->assertNotContains('grHttps',$filterNames);
		$this->assertNotContains('grSize',$filterNames);
		$this->assertNotContains('grDefault',$filterNames);
		$this->assertNotContains('grRating',$filterNames);
	}

	public function testRating(){
		$twigGravatar = new \TwigGravatar(null, null, null, 'x');

		$rated = $twigGravatar->avatar($this->email);
		$this->assertEquals($this->secureUrl."?rating=x", $rated);

		$rated = $twigGravatar->rating($this->url, "r");
		$this->assertEquals($this->url."?rating=r", $rated);
	}

	public function testUseHttpsIsFalse(){
		$twigGravatar = new \TwigGravatar(null, null, null, null, false);

		$unsecureUrl = $twigGravatar->avatar($this->email);
		$this->assertEquals($this->url, $unsecureUrl);

		$secureUrl = $twigGravatar->https($this->url);
		$this->assertEquals($this->secureUrl, $secureUrl);
	}

	public function testUseHttpsIsTrue(){
		$twigGravatar = new \TwigGravatar(null, null, null, null, true);

		$secureUrl = $twigGravatar->https($this->url);
		$this->assertEquals($this->secureUrl, $secureUrl);
	}

	public function testChaining(){
		$twigGravatar = new \TwigGravatar('blank', 200, 'sm', 'x', true);

		$secureUrl = $twigGravatar->avatar($this->email);
		$this->assertEquals($this->secureUrl.'?rating=x&size=200&default=blank', $secureUrl);

		$secureUrl = $twigGravatar->avatar($this->email);
		$secureUrl = $twigGravatar->rating($secureUrl, 'r');
		$this->assertEquals($this->secureUrl.'?rating=r&size=200&default=blank', $secureUrl);

		$secureUrl = $twigGravatar->avatar($this->email);
		$secureUrl = $twigGravatar->rating($secureUrl, 'r');
		$secureUrl = $twigGravatar->def($secureUrl, 'monsterid');
		$secureUrl = $twigGravatar->size($secureUrl, '20');
		$this->assertEquals($this->secureUrl.'?rating=r&size=20&default=monsterid', $secureUrl);

		$secureUrl = $twigGravatar->avatar($this->email);
		$secureUrl = $twigGravatar->def($secureUrl, 'monsterid', true);
		$secureUrl = $twigGravatar->size($secureUrl, '20');
		$secureUrl = $twigGravatar->rating($secureUrl, 'r');
		$this->assertEquals($this->secureUrl.'?rating=r&size=20&default=monsterid&forcedefault=y', $secureUrl);
	}
}
