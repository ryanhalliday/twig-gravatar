<?php

class TwigGravatar extends \Twig_Extension{
	public $baseUrl = "http://www.gravatar.com/";
	public $httpsUrl = "https://secure.gravatar.com/";

	/**
	 * {@inheritdoc}
	 */
	public function getFilters(){
		return array(
			'avatar' => new \Twig_Filter_Method($this,'avatar'),
			'https' => new \Twig_Filter_Method($this,'https'),
			'size' => new \Twig_Filter_Method($this,'size'),
		);
	}

	/**
	 * Get a Gravatar Avatar URL
	 * @param  string $email Gravatar Email address
	 * @return string        Gravatar Avatar URL
	 * @throws \InvalidArgumentException If $email is invalid
	 */
	public function avatar($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)){
			return $this->baseUrl . "avatar/" . $this->generateHash($email);
		}
		else throw new InvalidArgumentException("The avatar filter must be passed a valid Email address");
	}

	/**
	 * Change a Gravatar URL to its Secure version
	 * @param  string $value URL to convert
	 * @return string        Converted URL
	 * @throws \InvalidArgumentException If $value isn't an existing Gravatar URL
	 */
	public function https($value){
		if (strpos($value, $this->baseUrl) === false){
			throw new InvalidArgumentException("You can only convert existing Gravatar URLs to HTTPS");
		}
		else{
			return str_replace($this->baseUrl, $this->httpsUrl, $value);
		}
	}

	/**
	 * Change the Size of a Gravatar URL
	 * @param  string  $value
	 * @param  integer $px
	 * @return string Sized Gravatar URL
	 * @todo  Check if the ? is still needed
	 */
	public function size($value, $px){
		if (!is_numeric($px) && $px < 0 && $px > 2048){
			throw new InvalidArgumentException("You must pass the size filter a valid number between 0 and 2048");
		}
		else if (strpos($value,$this->baseUrl) === false
			&& strpos($value, $this->httpsUrl) === false){
			throw new InvalidArgumentException("You must pass the size filter an existing Gravatar URL");
		}
		else{
			return $value . "?size=" . $px;
		}
	}

	/**
	 * Generate the Hashed email address
	 * @param  string $email
	 * @return string        Hashed email address
	 */
	private function generateHash($email){
		return md5(strtolower(trim($email)));
	}

	/**
	 * Get the Extension name
	 * @return string
	 */
	public function getName(){
		return 'TwigGravatar';
	}
}